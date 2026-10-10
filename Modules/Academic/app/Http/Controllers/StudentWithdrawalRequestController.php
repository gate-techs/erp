<?php
namespace Modules\Academic\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Academic\Http\Requests\StoreStudentWithdrawalRequest;
use Modules\Academic\Http\Requests\UpdateStudentWithdrawalRequest;
use Modules\Academic\Models\StudentWithdrawalRequest;
use Modules\Academic\Services\StudentWithdrawalRequestService;
use Yajra\DataTables\Facades\DataTables;
class StudentWithdrawalRequestController extends Controller
{
    public function __construct(protected StudentWithdrawalRequestService $service){}
    public function index():View{return view('academic::withdrawal-requests.index',$this->service->formData());}
    public function create():View{return view('academic::withdrawal-requests.create',$this->service->formData());}
    public function edit(int $id):View{$request=$this->service->find($id);abort_unless($request,404,__('Withdrawal request not found.'));return view('academic::withdrawal-requests.edit',array_merge($this->service->formData(),compact('request')));}
    public function show(int $id):View{$request=$this->service->find($id);abort_unless($request,404,__('Withdrawal request not found.'));return view('academic::withdrawal-requests.show',compact('request'));}
    public function data(Request $request):JsonResponse{return DataTables::eloquent($this->service->query($request->all()))->addIndexColumn()->addColumn('institution_name',fn(StudentWithdrawalRequest $r)=>$r->institution?->name??'-')->addColumn('branch_name',fn(StudentWithdrawalRequest $r)=>$r->branch?->name??'-')->addColumn('student_name',fn(StudentWithdrawalRequest $r)=>$r->student?->name??'-')->editColumn('request_date',fn(StudentWithdrawalRequest $r)=>$r->request_date->format('Y-m-d'))->addColumn('status_badge',fn(StudentWithdrawalRequest $r)=>'<span class="badge bg-label-'.($r->status==='approved'?'success':($r->status==='rejected'?'danger':'warning')).'">'.e(__($r->status)).'</span>')->addColumn('actions',fn(StudentWithdrawalRequest $r)=>'<div class="d-flex justify-content-center gap-1"><a href="'.route('academic.withdrawal-requests.show',$r).'" class="btn btn-sm btn-icon btn-label-info"><i class="bx bx-show"></i></a><a href="'.route('academic.withdrawal-requests.edit',$r).'" class="btn btn-sm btn-icon btn-label-primary"><i class="bx bx-edit"></i></a><button class="btn btn-sm btn-icon btn-label-danger delete-withdrawal" data-id="'.$r->id.'"><i class="bx bx-trash"></i></button></div>')->rawColumns(['status_badge','actions'])->make(true);}
    public function store(StoreStudentWithdrawalRequest $request):RedirectResponse{$this->service->create($request->validated(),auth()->id());return redirect()->route('academic.withdrawal-requests.index')->with('success',__('Withdrawal request created successfully.'));}
    public function update(UpdateStudentWithdrawalRequest $request,int $id):RedirectResponse{$this->service->update($id,$request->validated());return redirect()->route('academic.withdrawal-requests.index')->with('success',__('Withdrawal request updated successfully.'));}
    public function destroy(int $id):JsonResponse{abort_unless($this->service->delete($id),404);return response()->json(['success'=>true,'message'=>__('Withdrawal request deleted successfully.')]);}
    public function branches(int $institutionId):JsonResponse{return response()->json($this->service->branches($institutionId)->map(fn($b)=>['id'=>$b->id,'name'=>$b->name]));}
    public function students(Request $request):JsonResponse{return response()->json($this->service->students($request->integer('institution_id')?:null,$request->integer('branch_id')?:null)->map(fn($s)=>['id'=>$s->id,'name'=>$s->name,'civil_id'=>$s->civil_id,'class_name'=>$s->class?->name,'guardian_name'=>$s->guardian?->name]));}
}
