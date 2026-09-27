<?php

/**
 * Official Astro Final Chart of Accounts Definition.
 * Generated from Excel: file/دليل حسابات استرو النهائي.xlsx
 * Total Accounts: 371
 */

return array (
  0 => 
  array (
    'code' => '1',
    'name_ar' => 'الأصول',
    'name_en' => 'Assets',
    'type' => 'asset',
    'is_group' => true,
    'children' => 
    array (
      0 => 
      array (
        'code' => '11',
        'name_ar' => 'الاصول المتداولة',
        'name_en' => 'Current Assets',
        'type' => 'asset',
        'is_group' => true,
        'children' => 
        array (
          0 => 
          array (
            'code' => '1101',
            'name_ar' => 'نقد فى الصندوق ولدى البنوك',
            'name_en' => 'Cash and Cash Equivalents',
            'type' => 'asset',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '110101',
                'name_ar' => 'الصندوق',
                'name_en' => 'Cash in Hand',
                'type' => 'asset',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '11010101',
                    'name_ar' => 'نقدية بالصندوق',
                    'name_en' => 'نقدية بالصندوق',
                    'type' => 'asset',
                    'is_group' => false,
                    'reconcile' => true,
                  ),
                ),
              ),
              1 => 
              array (
                'code' => '110102',
                'name_ar' => 'بنوك',
                'name_en' => 'Banks',
                'type' => 'asset',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '11010201',
                    'name_ar' => 'بنك بوبيان',
                    'name_en' => 'بنك بوبيان',
                    'type' => 'asset',
                    'is_group' => true,
                    'children' => 
                    array (
                      0 => 
                      array (
                        'code' => '1101020101',
                        'name_ar' => 'بنك بوبيان حساب رقم (0973110001)',
                        'name_en' => 'بنك بوبيان حساب رقم (0973110001)',
                        'type' => 'asset',
                        'is_group' => false,
                        'reconcile' => true,
                      ),
                      1 => 
                      array (
                        'code' => '1101020102',
                        'name_ar' => 'فيزا  بنك بوبيان  حساب رقم ( 422113XXXXXX1502',
                        'name_en' => 'فيزا  بنك بوبيان  حساب رقم ( 422113XXXXXX1502',
                        'type' => 'asset',
                        'is_group' => false,
                        'reconcile' => true,
                      ),
                    ),
                  ),
                  1 => 
                  array (
                    'code' => '11010202',
                    'name_ar' => 'بنك بيت التمويل',
                    'name_en' => 'بنك بيت التمويل',
                    'type' => 'asset',
                    'is_group' => true,
                    'children' => 
                    array (
                      0 => 
                      array (
                        'code' => '1101020201',
                        'name_ar' => 'بنك بيت التمويل الكويتي حساب رقم ( 011011119660 )',
                        'name_en' => 'بنك بيت التمويل الكويتي حساب رقم ( 011011119660 )',
                        'type' => 'asset',
                        'is_group' => false,
                        'reconcile' => true,
                      ),
                      1 => 
                      array (
                        'code' => '1101020202',
                        'name_ar' => 'فيزا  بنك بيت التمويل الكويتي',
                        'name_en' => 'فيزا  بنك بيت التمويل الكويتي',
                        'type' => 'asset',
                        'is_group' => false,
                        'reconcile' => true,
                      ),
                    ),
                  ),
                ),
              ),
              2 => 
              array (
                'code' => '110103',
                'name_ar' => 'عهد وخزائن نقدية',
                'name_en' => 'Petty Cash & Custodies',
                'type' => 'asset',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '11010301',
                    'name_ar' => 'عهدة نقدية - اشرف اكرم التيفاني',
                    'name_en' => 'عهدة نقدية - اشرف اكرم التيفاني',
                    'type' => 'asset',
                    'is_group' => false,
                    'reconcile' => true,
                  ),
                  1 => 
                  array (
                    'code' => '11010302',
                    'name_ar' => 'عهدة نقدية - حلمي   الهادي',
                    'name_en' => 'عهدة نقدية - حلمي   الهادي',
                    'type' => 'asset',
                    'is_group' => false,
                    'reconcile' => true,
                  ),
                  2 => 
                  array (
                    'code' => '11010303',
                    'name_ar' => 'عهدة نقدية - روان',
                    'name_en' => 'عهدة نقدية - روان',
                    'type' => 'asset',
                    'is_group' => false,
                    'reconcile' => true,
                  ),
                  3 => 
                  array (
                    'code' => '11010304',
                    'name_ar' => 'عهدة نقدية - حسن علي حسين',
                    'name_en' => 'عهدة نقدية - حسن علي حسين',
                    'type' => 'asset',
                    'is_group' => false,
                    'reconcile' => true,
                  ),
                ),
              ),
            ),
          ),
          1 => 
          array (
            'code' => '1104',
            'name_ar' => 'المدينون والذمم المدينة الأخرى',
            'name_en' => 'Accounts Receivable & Debtors',
            'type' => 'asset',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '110401',
                'name_ar' => 'مدينون',
                'name_en' => 'Debtors',
                'type' => 'asset',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '11040101',
                    'name_ar' => 'مدينون - طلاب',
                    'name_en' => 'مدينون - طلاب',
                    'type' => 'asset',
                    'is_group' => false,
                  ),
                ),
              ),
              1 => 
              array (
                'code' => '110402',
                'name_ar' => 'ذمم مدينة اخري',
                'name_en' => 'ذمم مدينة اخري',
                'type' => 'asset',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '11040201',
                    'name_ar' => 'مدينون ذمم/مبالغ قيد التحصيل عبر بوابات الدفع',
                    'name_en' => 'مدينون ذمم/مبالغ قيد التحصيل عبر بوابات الدفع',
                    'type' => 'asset',
                    'is_group' => true,
                    'children' => 
                    array (
                      0 => 
                      array (
                        'code' => '1104020101',
                        'name_ar' => 'منصة يوباي',
                        'name_en' => 'منصة يوباي',
                        'type' => 'asset',
                        'is_group' => false,
                        'reconcile' => true,
                      ),
                      1 => 
                      array (
                        'code' => '1104020102',
                        'name_ar' => 'منصة ديمة',
                        'name_en' => 'منصة ديمة',
                        'type' => 'asset',
                        'is_group' => false,
                        'reconcile' => true,
                      ),
                    ),
                  ),
                  1 => 
                  array (
                    'code' => '11040202',
                    'name_ar' => 'مصروفات مدفوعة مقدما',
                    'name_en' => 'مصروفات مدفوعة مقدما',
                    'type' => 'asset',
                    'is_group' => true,
                    'children' => 
                    array (
                      0 => 
                      array (
                        'code' => '1104020201',
                        'name_ar' => 'مصروفات مدفوعة مقدما - ايجارات',
                        'name_en' => 'مصروفات مدفوعة مقدما - ايجارات',
                        'type' => 'asset',
                        'is_group' => false,
                      ),
                      1 => 
                      array (
                        'code' => '1104020202',
                        'name_ar' => 'مصروفات مدفوعة مقدما - اخري',
                        'name_en' => 'مصروفات مدفوعة مقدما - اخري',
                        'type' => 'asset',
                        'is_group' => false,
                      ),
                    ),
                  ),
                  2 => 
                  array (
                    'code' => '11040203',
                    'name_ar' => 'ذمم موظفين',
                    'name_en' => 'ذمم موظفين',
                    'type' => 'asset',
                    'is_group' => true,
                    'children' => 
                    array (
                      0 => 
                      array (
                        'code' => '1104020301',
                        'name_ar' => 'ذمم الموظفين - قروض',
                        'name_en' => 'ذمم الموظفين - قروض',
                        'type' => 'asset',
                        'is_group' => false,
                      ),
                    ),
                  ),
                ),
              ),
            ),
          ),
          2 => 
          array (
            'code' => '1105',
            'name_ar' => 'إيرادات مستحقة',
            'name_en' => 'إيرادات مستحقة',
            'type' => 'asset',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '110501',
                'name_ar' => 'رسوم الطلاب المستحقة القبض',
                'name_en' => 'رسوم الطلاب المستحقة القبض',
                'type' => 'asset',
                'is_group' => false,
              ),
            ),
          ),
          3 => 
          array (
            'code' => '1106',
            'name_ar' => 'تامينات مستردة -ودائع تامين',
            'name_en' => 'تامينات مستردة -ودائع تامين',
            'type' => 'asset',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '110601',
                'name_ar' => 'تامينات مستردة - ايجارات',
                'name_en' => 'تامينات مستردة - ايجارات',
                'type' => 'asset',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '11060101',
                    'name_ar' => 'تامينات مستردة - ايجار حضانة - أبو فطيرة',
                    'name_en' => 'تامينات مستردة - ايجار حضانة - أبو فطيرة',
                    'type' => 'asset',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '11060102',
                    'name_ar' => 'تامينات مستردة - ايجار حضانة - الزهراء',
                    'name_en' => 'تامينات مستردة - ايجار حضانة - الزهراء',
                    'type' => 'asset',
                    'is_group' => false,
                  ),
                ),
              ),
              1 => 
              array (
                'code' => '110602',
                'name_ar' => 'ودائع تامينية',
                'name_en' => 'ودائع تامينية',
                'type' => 'asset',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '11060201',
                    'name_ar' => 'وديعة تامين وزارة الشئون الاجتماعية والعمل',
                    'name_en' => 'وديعة تامين وزارة الشئون الاجتماعية والعمل',
                    'type' => 'asset',
                    'is_group' => false,
                  ),
                ),
              ),
            ),
          ),
          4 => 
          array (
            'code' => '1107',
            'name_ar' => 'خطابات الضمان البنكية',
            'name_en' => 'خطابات الضمان البنكية',
            'type' => 'asset',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '110701',
                'name_ar' => 'خطابات الضمان البنكية',
                'name_en' => 'خطابات الضمان البنكية',
                'type' => 'asset',
                'is_group' => false,
                'reconcile' => true,
              ),
            ),
          ),
          5 => 
          array (
            'code' => '1108',
            'name_ar' => 'المخزون',
            'name_en' => 'المخزون',
            'type' => 'asset',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '110801',
                'name_ar' => 'المخزون',
                'name_en' => 'المخزون',
                'type' => 'asset',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '11080101',
                    'name_ar' => 'المخزون',
                    'name_en' => 'المخزون',
                    'type' => 'asset',
                    'is_group' => false,
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
      1 => 
      array (
        'code' => '12',
        'name_ar' => 'الاصول الغير  المتدولة',
        'name_en' => 'Non-Current Assets (Fixed Assets)',
        'type' => 'asset',
        'is_group' => true,
        'children' => 
        array (
          0 => 
          array (
            'code' => '1201',
            'name_ar' => 'الأصول الثابتة',
            'name_en' => 'الأصول الثابتة',
            'type' => 'asset',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '120101',
                'name_ar' => 'اصول اثاث ومفروشات',
                'name_en' => 'اصول اثاث ومفروشات',
                'type' => 'asset',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '12010101',
                    'name_ar' => 'تكلفة اصول اثاث ومفروشات',
                    'name_en' => 'تكلفة اصول اثاث ومفروشات',
                    'type' => 'asset',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '12010102',
                    'name_ar' => 'مجمع اهلاك اصول اثاث ومفروشات',
                    'name_en' => 'مجمع اهلاك اصول اثاث ومفروشات',
                    'type' => 'asset',
                    'is_group' => false,
                  ),
                ),
              ),
              1 => 
              array (
                'code' => '120102',
                'name_ar' => 'اصول أجهزة ومعدات',
                'name_en' => 'اصول أجهزة ومعدات',
                'type' => 'asset',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '12010201',
                    'name_ar' => 'تكلفة اصول أجهزة ومعدات',
                    'name_en' => 'تكلفة اصول أجهزة ومعدات',
                    'type' => 'asset',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '12010202',
                    'name_ar' => 'مجمع اهلاك اصول أجهزة ومعدات',
                    'name_en' => 'مجمع اهلاك اصول أجهزة ومعدات',
                    'type' => 'asset',
                    'is_group' => false,
                  ),
                ),
              ),
              2 => 
              array (
                'code' => '120103',
                'name_ar' => 'اصول كمبيوترات وطابعات وأجهزة الكترونية',
                'name_en' => 'اصول كمبيوترات وطابعات وأجهزة الكترونية',
                'type' => 'asset',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '12010301',
                    'name_ar' => 'تكلفة اصول كمبيوترات وطابعات وأجهزة الكترونية',
                    'name_en' => 'تكلفة اصول كمبيوترات وطابعات وأجهزة الكترونية',
                    'type' => 'asset',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '12010302',
                    'name_ar' => 'مجمع اهلاك اصول كمبيوترات وطابعات وأجهزة الكترونية',
                    'name_en' => 'مجمع اهلاك اصول كمبيوترات وطابعات وأجهزة الكترونية',
                    'type' => 'asset',
                    'is_group' => false,
                  ),
                ),
              ),
              3 => 
              array (
                'code' => '120104',
                'name_ar' => 'اصول سيارت',
                'name_en' => 'اصول سيارت',
                'type' => 'asset',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '12010401',
                    'name_ar' => 'تكلفة اصول سيارت',
                    'name_en' => 'تكلفة اصول سيارت',
                    'type' => 'asset',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '12010402',
                    'name_ar' => 'مجمع اهلاك اصول سيارت',
                    'name_en' => 'مجمع اهلاك اصول سيارت',
                    'type' => 'asset',
                    'is_group' => false,
                  ),
                ),
              ),
              4 => 
              array (
                'code' => '120105',
                'name_ar' => 'أصول معدات تعليمية',
                'name_en' => 'أصول معدات تعليمية',
                'type' => 'asset',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '12010501',
                    'name_ar' => 'تكلفة أصول معدات تعليمية',
                    'name_en' => 'تكلفة أصول معدات تعليمية',
                    'type' => 'asset',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '12010502',
                    'name_ar' => 'مجمع اهلاك اصول معدات تعليمية',
                    'name_en' => 'مجمع اهلاك اصول معدات تعليمية',
                    'type' => 'asset',
                    'is_group' => false,
                  ),
                ),
              ),
              5 => 
              array (
                'code' => '120106',
                'name_ar' => 'اصول - تحسينات على العين الماجرة',
                'name_en' => 'اصول - تحسينات على العين الماجرة',
                'type' => 'asset',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '12010601',
                    'name_ar' => 'تكلفة اصول - تحسينات على العين الماجرة',
                    'name_en' => 'تكلفة اصول - تحسينات على العين الماجرة',
                    'type' => 'asset',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '12010602',
                    'name_ar' => 'مجمع اهلاك اصول - تحسينات على العين الماجرة',
                    'name_en' => 'مجمع اهلاك اصول - تحسينات على العين الماجرة',
                    'type' => 'asset',
                    'is_group' => false,
                  ),
                ),
              ),
            ),
          ),
          1 => 
          array (
            'code' => '1202',
            'name_ar' => 'الاصول غير الملموسة',
            'name_en' => 'الاصول غير الملموسة',
            'type' => 'asset',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '120201',
                'name_ar' => 'اصول غير ملموسة -  مواقع وبرامج كمبيوتر',
                'name_en' => 'اصول غير ملموسة -  مواقع وبرامج كمبيوتر',
                'type' => 'asset',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '12020101',
                    'name_ar' => 'تكلفة اصول غير ملموسة -  مواقع وبرامج كمبيوتر',
                    'name_en' => 'تكلفة اصول غير ملموسة -  مواقع وبرامج كمبيوتر',
                    'type' => 'asset',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '12020102',
                    'name_ar' => 'مجمع اطفاء اصول غير ملموسة -  مواقع وبرامج كمبيوتر',
                    'name_en' => 'مجمع اطفاء اصول غير ملموسة -  مواقع وبرامج كمبيوتر',
                    'type' => 'asset',
                    'is_group' => false,
                  ),
                ),
              ),
              1 => 
              array (
                'code' => '120202',
                'name_ar' => 'اصول غير ملموسة -  تكلفة الترخيص',
                'name_en' => 'اصول غير ملموسة -  تكلفة الترخيص',
                'type' => 'asset',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '12020201',
                    'name_ar' => 'تكلفة اصول غير ملموسة -  تكلفة الترخيص',
                    'name_en' => 'تكلفة اصول غير ملموسة -  تكلفة الترخيص',
                    'type' => 'asset',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '12020202',
                    'name_ar' => 'مجمع اطفاء اصول غير ملموسة -  تكلفة الترخيص',
                    'name_en' => 'مجمع اطفاء اصول غير ملموسة -  تكلفة الترخيص',
                    'type' => 'asset',
                    'is_group' => false,
                  ),
                ),
              ),
              2 => 
              array (
                'code' => '120203',
                'name_ar' => 'اصول غير ملموسة -  الشهرة',
                'name_en' => 'اصول غير ملموسة -  الشهرة',
                'type' => 'asset',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '12020301',
                    'name_ar' => 'تكلفة اصول غير ملموسة -  الشهرة',
                    'name_en' => 'تكلفة اصول غير ملموسة -  الشهرة',
                    'type' => 'asset',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '12020302',
                    'name_ar' => 'مجمع اطفاء اصول غير ملموسة -  الشهرة',
                    'name_en' => 'مجمع اطفاء اصول غير ملموسة -  الشهرة',
                    'type' => 'asset',
                    'is_group' => false,
                  ),
                ),
              ),
              3 => 
              array (
                'code' => '120204',
                'name_ar' => 'اصول غير ملموسة -  رخص النظام',
                'name_en' => 'اصول غير ملموسة -  رخص النظام',
                'type' => 'asset',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '12020401',
                    'name_ar' => 'تكلفة اصول غير ملموسة -  رخص النظام',
                    'name_en' => 'تكلفة اصول غير ملموسة -  رخص النظام',
                    'type' => 'asset',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '12020402',
                    'name_ar' => 'مجمع اطفاء اصول غير ملموسة -  رخص النظام',
                    'name_en' => 'مجمع اطفاء اصول غير ملموسة -  رخص النظام',
                    'type' => 'asset',
                    'is_group' => false,
                  ),
                ),
              ),
              4 => 
              array (
                'code' => '120205',
                'name_ar' => 'اصول غير ملموسة -  حق استخدام الأصل',
                'name_en' => 'اصول غير ملموسة -  حق استخدام الأصل',
                'type' => 'asset',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '12020501',
                    'name_ar' => 'تكلفة اصول غير ملموسة -  حق استخدام الأصل',
                    'name_en' => 'تكلفة اصول غير ملموسة -  حق استخدام الأصل',
                    'type' => 'asset',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '12020502',
                    'name_ar' => 'مجمع اطفاء اصول غير ملموسة -  حق استخدام الأصل',
                    'name_en' => 'مجمع اطفاء اصول غير ملموسة -  حق استخدام الأصل',
                    'type' => 'asset',
                    'is_group' => false,
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    ),
  ),
  1 => 
  array (
    'code' => '2',
    'name_ar' => 'الالتزامات',
    'name_en' => 'Liabilities',
    'type' => 'liability',
    'is_group' => true,
    'children' => 
    array (
      0 => 
      array (
        'code' => '21',
        'name_ar' => 'الالتزامات المتدولة',
        'name_en' => 'Current Liabilities',
        'type' => 'liability',
        'is_group' => true,
        'children' => 
        array (
          0 => 
          array (
            'code' => '2101',
            'name_ar' => 'دائنون وارصدة دائنة اخري',
            'name_en' => 'Trade Creditors & Suppliers',
            'type' => 'liability',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '210101',
                'name_ar' => 'مستحقات الموردين',
                'name_en' => 'مستحقات الموردين',
                'type' => 'liability',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '21010101',
                    'name_ar' => 'الموردين',
                    'name_en' => 'الموردين',
                    'type' => 'liability',
                    'is_group' => false,
                  ),
                ),
              ),
            ),
          ),
          1 => 
          array (
            'code' => '2102',
            'name_ar' => 'قروض قصيرة الاجل',
            'name_en' => 'Accrued Expenses & Provisions',
            'type' => 'liability',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '210201',
                'name_ar' => 'قروض قصير الاجل - بنوك',
                'name_en' => 'قروض قصير الاجل - بنوك',
                'type' => 'liability',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '21020101',
                    'name_ar' => 'قروض قصير الاجل - بنك بوبيان',
                    'name_en' => 'قروض قصير الاجل - بنك بوبيان',
                    'type' => 'liability',
                    'is_group' => false,
                    'reconcile' => true,
                  ),
                ),
              ),
              1 => 
              array (
                'code' => '210202',
                'name_ar' => 'فوائد القروض قصيرة الاجل - بنوك',
                'name_en' => 'فوائد القروض قصيرة الاجل - بنوك',
                'type' => 'liability',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '21020201',
                    'name_ar' => 'فوائد القروض قصيرة الاجل - بنك بوبيان',
                    'name_en' => 'فوائد القروض قصيرة الاجل - بنك بوبيان',
                    'type' => 'liability',
                    'is_group' => false,
                    'reconcile' => true,
                  ),
                ),
              ),
            ),
          ),
          2 => 
          array (
            'code' => '2103',
            'name_ar' => 'ارصدة دائنة اخري',
            'name_en' => 'Unearned Revenues',
            'type' => 'liability',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '210301',
                'name_ar' => 'دائنو الموظفين',
                'name_en' => 'دائنو الموظفين',
                'type' => 'liability',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '21030101',
                    'name_ar' => 'الضمان الاجتماعي - الموظفين',
                    'name_en' => 'الضمان الاجتماعي - الموظفين',
                    'type' => 'liability',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '21030102',
                    'name_ar' => 'الرواتب المستحقة',
                    'name_en' => 'الرواتب المستحقة',
                    'type' => 'liability',
                    'is_group' => false,
                  ),
                  2 => 
                  array (
                    'code' => '21030103',
                    'name_ar' => 'صندوق الجزاءات',
                    'name_en' => 'صندوق الجزاءات',
                    'type' => 'liability',
                    'is_group' => false,
                    'reconcile' => true,
                  ),
                  3 => 
                  array (
                    'code' => '21030104',
                    'name_ar' => 'امانات الموظفين',
                    'name_en' => 'امانات الموظفين',
                    'type' => 'liability',
                    'is_group' => false,
                  ),
                  4 => 
                  array (
                    'code' => '21030105',
                    'name_ar' => 'مدفوعات الموظفين الأخرى',
                    'name_en' => 'مدفوعات الموظفين الأخرى',
                    'type' => 'liability',
                    'is_group' => false,
                  ),
                ),
              ),
              1 => 
              array (
                'code' => '210302',
                'name_ar' => 'شيكات لم تقدم للصرف',
                'name_en' => 'شيكات لم تقدم للصرف',
                'type' => 'liability',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '21030201',
                    'name_ar' => 'شيكات لم تقدم للصرف',
                    'name_en' => 'شيكات لم تقدم للصرف',
                    'type' => 'liability',
                    'is_group' => false,
                  ),
                ),
              ),
              2 => 
              array (
                'code' => '210303',
                'name_ar' => 'مخصص الاجازات للموظفين',
                'name_en' => 'مخصص الاجازات للموظفين',
                'type' => 'liability',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '21030301',
                    'name_ar' => 'مخصص الاجازات للموظفين',
                    'name_en' => 'مخصص الاجازات للموظفين',
                    'type' => 'liability',
                    'is_group' => false,
                  ),
                ),
              ),
            ),
          ),
          3 => 
          array (
            'code' => '2104',
            'name_ar' => 'المصروفات المستحقة',
            'name_en' => 'المصروفات المستحقة',
            'type' => 'liability',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '210401',
                'name_ar' => 'المصروفات المستحقة - الإيجارات',
                'name_en' => 'المصروفات المستحقة - الإيجارات',
                'type' => 'liability',
                'is_group' => false,
              ),
              1 => 
              array (
                'code' => '210402',
                'name_ar' => 'المصروفات المستحقة - الاخري',
                'name_en' => 'المصروفات المستحقة - الاخري',
                'type' => 'liability',
                'is_group' => false,
              ),
            ),
          ),
          4 => 
          array (
            'code' => '2105',
            'name_ar' => 'رسوم الطلاب',
            'name_en' => 'رسوم الطلاب',
            'type' => 'liability',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '210501',
                'name_ar' => 'رسوم التسجيل المقدم',
                'name_en' => 'رسوم التسجيل المقدم',
                'type' => 'liability',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '21050101',
                    'name_ar' => 'رسوم التسجيل المقدم للطلاب',
                    'name_en' => 'رسوم التسجيل المقدم للطلاب',
                    'type' => 'liability',
                    'is_group' => false,
                  ),
                ),
              ),
              1 => 
              array (
                'code' => '210502',
                'name_ar' => 'رسوم دراسية مقدمة',
                'name_en' => 'رسوم دراسية مقدمة',
                'type' => 'liability',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '21050201',
                    'name_ar' => 'رسوم دراسية مقدمة للطلاب',
                    'name_en' => 'رسوم دراسية مقدمة للطلاب',
                    'type' => 'liability',
                    'is_group' => false,
                  ),
                ),
              ),
              2 => 
              array (
                'code' => '210503',
                'name_ar' => 'رسوم تقييم مقدمة',
                'name_en' => 'رسوم تقييم مقدمة',
                'type' => 'liability',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '21050301',
                    'name_ar' => 'رسوم تقييم مقدمة للطلاب',
                    'name_en' => 'رسوم تقييم مقدمة للطلاب',
                    'type' => 'liability',
                    'is_group' => false,
                  ),
                ),
              ),
              3 => 
              array (
                'code' => '210504',
                'name_ar' => 'رسوم طلاب مستحقة الدفع',
                'name_en' => 'رسوم طلاب مستحقة الدفع',
                'type' => 'liability',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '21050401',
                    'name_ar' => 'رسوم الطلاب مستحقة الدفع على اقساط السنة الحالية',
                    'name_en' => 'رسوم الطلاب مستحقة الدفع على اقساط السنة الحالية',
                    'type' => 'liability',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '21050402',
                    'name_ar' => 'رسوم الطلاب مستحقة الدفع على اقساط العام المقبل',
                    'name_en' => 'رسوم الطلاب مستحقة الدفع على اقساط العام المقبل',
                    'type' => 'liability',
                    'is_group' => false,
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
      1 => 
      array (
        'code' => '22',
        'name_ar' => 'الالتزامات طويلة الاجل',
        'name_en' => 'Non-Current Liabilities',
        'type' => 'liability',
        'is_group' => true,
        'children' => 
        array (
          0 => 
          array (
            'code' => '2201',
            'name_ar' => 'مخصصات',
            'name_en' => 'مخصصات',
            'type' => 'liability',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '220101',
                'name_ar' => 'مخصصات نهاية الخدمة للموظفين',
                'name_en' => 'مخصصات نهاية الخدمة للموظفين',
                'type' => 'liability',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '22010101',
                    'name_ar' => 'مخصصات نهاية الخدمة للموظفين',
                    'name_en' => 'مخصصات نهاية الخدمة للموظفين',
                    'type' => 'liability',
                    'is_group' => false,
                  ),
                ),
              ),
              1 => 
              array (
                'code' => '220102',
                'name_ar' => 'مخصص ديون معدومة',
                'name_en' => 'مخصص ديون معدومة',
                'type' => 'liability',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '22010201',
                    'name_ar' => 'مخصص ديون معدومة',
                    'name_en' => 'مخصص ديون معدومة',
                    'type' => 'liability',
                    'is_group' => false,
                  ),
                ),
              ),
              2 => 
              array (
                'code' => '220103',
                'name_ar' => 'مخصصات المخزون',
                'name_en' => 'مخصصات المخزون',
                'type' => 'liability',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '22010301',
                    'name_ar' => 'مخصصات المخزون',
                    'name_en' => 'مخصصات المخزون',
                    'type' => 'liability',
                    'is_group' => false,
                  ),
                ),
              ),
            ),
          ),
          1 => 
          array (
            'code' => '2202',
            'name_ar' => 'التزام الإيجارات',
            'name_en' => 'التزام الإيجارات',
            'type' => 'liability',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '220201',
                'name_ar' => 'التزام الإيجارات',
                'name_en' => 'التزام الإيجارات',
                'type' => 'liability',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '22020101',
                    'name_ar' => 'التزام ايجار حضانة - أبو فطيرة',
                    'name_en' => 'التزام ايجار حضانة - أبو فطيرة',
                    'type' => 'liability',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '22020102',
                    'name_ar' => 'التزام ايجار حضانة - الزهراء',
                    'name_en' => 'التزام ايجار حضانة - الزهراء',
                    'type' => 'liability',
                    'is_group' => false,
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
      2 => 
      array (
        'code' => '23',
        'name_ar' => 'قروض طويلة الاجل',
        'name_en' => 'قروض طويلة الاجل',
        'type' => 'liability',
        'is_group' => true,
        'children' => 
        array (
          0 => 
          array (
            'code' => '2301',
            'name_ar' => 'مستحقات القروض طويلة الاجل',
            'name_en' => 'مستحقات القروض طويلة الاجل',
            'type' => 'liability',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '230101',
                'name_ar' => 'قرض طويل الاجل بنك بوبيان',
                'name_en' => 'قرض طويل الاجل بنك بوبيان',
                'type' => 'liability',
                'is_group' => false,
                'reconcile' => true,
              ),
            ),
          ),
        ),
      ),
    ),
  ),
  2 => 
  array (
    'code' => '3',
    'name_ar' => 'حقوق الملكية',
    'name_en' => 'Equity',
    'type' => 'equity',
    'is_group' => true,
    'children' => 
    array (
      0 => 
      array (
        'code' => '31',
        'name_ar' => 'راس المال',
        'name_en' => 'Capital',
        'type' => 'equity',
        'is_group' => true,
        'children' => 
        array (
          0 => 
          array (
            'code' => '3101',
            'name_ar' => 'راس المال الشركة',
            'name_en' => 'Company Capital',
            'type' => 'equity',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '310101',
                'name_ar' => 'حصة راس مال الشركة',
                'name_en' => 'حصة راس مال الشركة',
                'type' => 'equity',
                'is_group' => false,
              ),
            ),
          ),
        ),
      ),
      1 => 
      array (
        'code' => '32',
        'name_ar' => 'حسابات الشركاء',
        'name_en' => 'Partners Accounts',
        'type' => 'equity',
        'is_group' => true,
        'children' => 
        array (
          0 => 
          array (
            'code' => '3201',
            'name_ar' => 'حسابات جاري الشركاء',
            'name_en' => 'حسابات جاري الشركاء',
            'type' => 'equity',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '320101',
                'name_ar' => 'جاري الشريك -  خالد وليد السعيد',
                'name_en' => 'جاري الشريك -  خالد وليد السعيد',
                'type' => 'equity',
                'is_group' => false,
              ),
            ),
          ),
        ),
      ),
      2 => 
      array (
        'code' => '33',
        'name_ar' => 'الارباح المحتجزة',
        'name_en' => 'Retained Earnings',
        'type' => 'equity',
        'is_group' => true,
        'children' => 
        array (
          0 => 
          array (
            'code' => '3301',
            'name_ar' => 'الارباح المحتجزة',
            'name_en' => 'الارباح المحتجزة',
            'type' => 'equity',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '330101',
                'name_ar' => 'الارباح المحتجزة',
                'name_en' => 'الارباح المحتجزة',
                'type' => 'equity',
                'is_group' => false,
              ),
            ),
          ),
        ),
      ),
      3 => 
      array (
        'code' => '34',
        'name_ar' => 'احتياطى راس المال',
        'name_en' => 'Capital Reserves',
        'type' => 'equity',
        'is_group' => true,
        'children' => 
        array (
          0 => 
          array (
            'code' => '3401',
            'name_ar' => 'الاحتياطي القانوني',
            'name_en' => 'الاحتياطي القانوني',
            'type' => 'equity',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '340101',
                'name_ar' => 'الاحتياطي القانوني',
                'name_en' => 'الاحتياطي القانوني',
                'type' => 'equity',
                'is_group' => false,
              ),
            ),
          ),
          1 => 
          array (
            'code' => '3402',
            'name_ar' => 'احتياطي اختياري',
            'name_en' => 'احتياطي اختياري',
            'type' => 'equity',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '340201',
                'name_ar' => 'احتياطي اختياري',
                'name_en' => 'احتياطي اختياري',
                'type' => 'equity',
                'is_group' => false,
              ),
            ),
          ),
        ),
      ),
    ),
  ),
  3 => 
  array (
    'code' => '4',
    'name_ar' => 'الايرادات',
    'name_en' => 'Revenues',
    'type' => 'income',
    'is_group' => true,
    'children' => 
    array (
      0 => 
      array (
        'code' => '41',
        'name_ar' => 'الايرادات',
        'name_en' => 'Operating Revenues',
        'type' => 'income',
        'is_group' => true,
        'children' => 
        array (
          0 => 
          array (
            'code' => '4101',
            'name_ar' => 'الايرادات الاكاديمية',
            'name_en' => 'الايرادات الاكاديمية',
            'type' => 'income',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '410101',
                'name_ar' => 'ايرادات الرسوم الدراسية',
                'name_en' => 'ايرادات الرسوم الدراسية',
                'type' => 'income',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '41010101',
                    'name_ar' => 'دخل الرسوم الدراسية',
                    'name_en' => 'دخل الرسوم الدراسية',
                    'type' => 'income',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '41010102',
                    'name_ar' => 'استرداد الرسوم الدراسية',
                    'name_en' => 'استرداد الرسوم الدراسية',
                    'type' => 'income',
                    'is_group' => false,
                  ),
                  2 => 
                  array (
                    'code' => '41010103',
                    'name_ar' => 'خصم الرسوم الدراسية',
                    'name_en' => 'خصم الرسوم الدراسية',
                    'type' => 'income',
                    'is_group' => false,
                  ),
                ),
              ),
              1 => 
              array (
                'code' => '410102',
                'name_ar' => 'ايرادات رسوم التسجيل',
                'name_en' => 'ايرادات رسوم التسجيل',
                'type' => 'income',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '41010201',
                    'name_ar' => 'دخل رسوم التسجيل',
                    'name_en' => 'دخل رسوم التسجيل',
                    'type' => 'income',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '41010202',
                    'name_ar' => 'استرداد رسوم التسجيل',
                    'name_en' => 'استرداد رسوم التسجيل',
                    'type' => 'income',
                    'is_group' => false,
                  ),
                ),
              ),
              2 => 
              array (
                'code' => '410103',
                'name_ar' => 'ايرادات رسوم التقييم',
                'name_en' => 'ايرادات رسوم التقييم',
                'type' => 'income',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '41010301',
                    'name_ar' => 'دخل رسوم التقييم',
                    'name_en' => 'دخل رسوم التقييم',
                    'type' => 'income',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '41010302',
                    'name_ar' => 'استرداد رسوم التقييم',
                    'name_en' => 'استرداد رسوم التقييم',
                    'type' => 'income',
                    'is_group' => false,
                  ),
                ),
              ),
            ),
          ),
          1 => 
          array (
            'code' => '4102',
            'name_ar' => 'الايرادات غير الاكاديمية',
            'name_en' => 'الايرادات غير الاكاديمية',
            'type' => 'income',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '410201',
                'name_ar' => 'ايرادات رسوم الأنشطة والفعاليات',
                'name_en' => 'ايرادات رسوم الأنشطة والفعاليات',
                'type' => 'income',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '41020101',
                    'name_ar' => 'إيرادات الرحلات',
                    'name_en' => 'إيرادات الرحلات',
                    'type' => 'income',
                    'is_group' => true,
                    'children' => 
                    array (
                      0 => 
                      array (
                        'code' => '4102010101',
                        'name_ar' => 'دخل رسوم الرحلات الصباحي',
                        'name_en' => 'دخل رسوم الرحلات الصباحي',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                      1 => 
                      array (
                        'code' => '4102010102',
                        'name_ar' => 'استرداد رسوم الرحلات الصباحي',
                        'name_en' => 'استرداد رسوم الرحلات الصباحي',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                      2 => 
                      array (
                        'code' => '4102010103',
                        'name_ar' => 'دخل رسوم الرحلات المسائي',
                        'name_en' => 'دخل رسوم الرحلات المسائي',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                      3 => 
                      array (
                        'code' => '4102010104',
                        'name_ar' => 'استرداد رسوم الرحلات المسائي',
                        'name_en' => 'استرداد رسوم الرحلات المسائي',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                    ),
                  ),
                  1 => 
                  array (
                    'code' => '41020102',
                    'name_ar' => 'ايرادات المخيمات الصيفية والشتوية',
                    'name_en' => 'ايرادات المخيمات الصيفية والشتوية',
                    'type' => 'income',
                    'is_group' => true,
                    'children' => 
                    array (
                      0 => 
                      array (
                        'code' => '4102010201',
                        'name_ar' => 'دخل المخيم الصيفي صباحي',
                        'name_en' => 'دخل المخيم الصيفي صباحي',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                      1 => 
                      array (
                        'code' => '4102010202',
                        'name_ar' => 'استرداد رسوم المخيم الصيفي صباحي',
                        'name_en' => 'استرداد رسوم المخيم الصيفي صباحي',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                      2 => 
                      array (
                        'code' => '4102010203',
                        'name_ar' => 'دخل رسوم المخيم الشتوي الصباحي',
                        'name_en' => 'دخل رسوم المخيم الشتوي الصباحي',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                      3 => 
                      array (
                        'code' => '4102010204',
                        'name_ar' => 'استرداد رسوم المخيم الشتوي الصباحي',
                        'name_en' => 'استرداد رسوم المخيم الشتوي الصباحي',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                      4 => 
                      array (
                        'code' => '4102010205',
                        'name_ar' => 'دخل المخيم الصيفي مسائي',
                        'name_en' => 'دخل المخيم الصيفي مسائي',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                      5 => 
                      array (
                        'code' => '4102010206',
                        'name_ar' => 'استرداد رسوم المخيم الصيفي مسائي',
                        'name_en' => 'استرداد رسوم المخيم الصيفي مسائي',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                      6 => 
                      array (
                        'code' => '4102010207',
                        'name_ar' => 'دخل رسوم المخيم الشتوي المسائي',
                        'name_en' => 'دخل رسوم المخيم الشتوي المسائي',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                      7 => 
                      array (
                        'code' => '4102010208',
                        'name_ar' => 'استرداد رسوم المخيم الشتوي المسائي',
                        'name_en' => 'استرداد رسوم المخيم الشتوي المسائي',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                    ),
                  ),
                  2 => 
                  array (
                    'code' => '41020103',
                    'name_ar' => 'إيرادات أعياد الميلاد',
                    'name_en' => 'إيرادات أعياد الميلاد',
                    'type' => 'income',
                    'is_group' => true,
                    'children' => 
                    array (
                      0 => 
                      array (
                        'code' => '4102010301',
                        'name_ar' => 'دخل رسوم أعياد الميلاد',
                        'name_en' => 'دخل رسوم أعياد الميلاد',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                      1 => 
                      array (
                        'code' => '4102010302',
                        'name_ar' => 'استرداد رسوم أعياد الميلاد',
                        'name_en' => 'استرداد رسوم أعياد الميلاد',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                    ),
                  ),
                  3 => 
                  array (
                    'code' => '41020104',
                    'name_ar' => 'إيرادات مخيم رمضان',
                    'name_en' => 'إيرادات مخيم رمضان',
                    'type' => 'income',
                    'is_group' => true,
                    'children' => 
                    array (
                      0 => 
                      array (
                        'code' => '4102010401',
                        'name_ar' => 'دخل مخيم رمضاني قبل الافطار',
                        'name_en' => 'دخل مخيم رمضاني قبل الافطار',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                      1 => 
                      array (
                        'code' => '4102010402',
                        'name_ar' => 'استرداد مخيم رمضاني قبل الافطار',
                        'name_en' => 'استرداد مخيم رمضاني قبل الافطار',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                      2 => 
                      array (
                        'code' => '4102010403',
                        'name_ar' => 'دخل مخيم رمضاني بعد الافطار',
                        'name_en' => 'دخل مخيم رمضاني بعد الافطار',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                      3 => 
                      array (
                        'code' => '4102010404',
                        'name_ar' => 'استرداد مخيم رمضاني بعد الافطار',
                        'name_en' => 'استرداد مخيم رمضاني بعد الافطار',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                    ),
                  ),
                  4 => 
                  array (
                    'code' => '41020105',
                    'name_ar' => 'إيرادات حفلة التخرج',
                    'name_en' => 'إيرادات حفلة التخرج',
                    'type' => 'income',
                    'is_group' => true,
                    'children' => 
                    array (
                      0 => 
                      array (
                        'code' => '4102010501',
                        'name_ar' => 'دخل حفلة  التخرج',
                        'name_en' => 'دخل حفلة  التخرج',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                      1 => 
                      array (
                        'code' => '4102010502',
                        'name_ar' => 'استرداد حفل التخرج',
                        'name_en' => 'استرداد حفل التخرج',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                    ),
                  ),
                  5 => 
                  array (
                    'code' => '41020106',
                    'name_ar' => 'إيرادات نادي بعد المدرسة',
                    'name_en' => 'إيرادات نادي بعد المدرسة',
                    'type' => 'income',
                    'is_group' => true,
                    'children' => 
                    array (
                      0 => 
                      array (
                        'code' => '4102010601',
                        'name_ar' => 'دخل نادي بعد المدرسة',
                        'name_en' => 'دخل نادي بعد المدرسة',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                      1 => 
                      array (
                        'code' => '4102010602',
                        'name_ar' => 'استرداد نادي بعد المدرسة',
                        'name_en' => 'استرداد نادي بعد المدرسة',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                    ),
                  ),
                ),
              ),
              1 => 
              array (
                'code' => '410202',
                'name_ar' => 'إيرادات مبيعات المستلزمات الطلابية',
                'name_en' => 'إيرادات مبيعات المستلزمات الطلابية',
                'type' => 'income',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '41020201',
                    'name_ar' => 'إيرادات مبيعات الزي الرسمي للطلاب',
                    'name_en' => 'إيرادات مبيعات الزي الرسمي للطلاب',
                    'type' => 'income',
                    'is_group' => true,
                    'children' => 
                    array (
                      0 => 
                      array (
                        'code' => '4102020101',
                        'name_ar' => 'دخل مبيعات الزي الرسمي للطلاب',
                        'name_en' => 'دخل مبيعات الزي الرسمي للطلاب',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                      1 => 
                      array (
                        'code' => '4102020102',
                        'name_ar' => 'استرداد مبيعات الرسمي للطلاب',
                        'name_en' => 'استرداد مبيعات الرسمي للطلاب',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                    ),
                  ),
                  1 => 
                  array (
                    'code' => '41020202',
                    'name_ar' => 'ايرادات مبيعات الكتاب الدراسية',
                    'name_en' => 'ايرادات مبيعات الكتاب الدراسية',
                    'type' => 'income',
                    'is_group' => true,
                    'children' => 
                    array (
                      0 => 
                      array (
                        'code' => '4102020201',
                        'name_ar' => 'دخل مبيعات  الكتب الدراسية',
                        'name_en' => 'دخل مبيعات  الكتب الدراسية',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                      1 => 
                      array (
                        'code' => '4102020202',
                        'name_ar' => 'استرداد مبيعات الكتاب الدراسية',
                        'name_en' => 'استرداد مبيعات الكتاب الدراسية',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                    ),
                  ),
                  2 => 
                  array (
                    'code' => '41020203',
                    'name_ar' => 'ايرادات مبيعات الصور السنوية',
                    'name_en' => 'ايرادات مبيعات الصور السنوية',
                    'type' => 'income',
                    'is_group' => true,
                    'children' => 
                    array (
                      0 => 
                      array (
                        'code' => '4102020301',
                        'name_ar' => 'دخل مبيعات الصور السنوية',
                        'name_en' => 'دخل مبيعات الصور السنوية',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                      1 => 
                      array (
                        'code' => '4102020302',
                        'name_ar' => 'استرداد مبيعات الصور السنوية',
                        'name_en' => 'استرداد مبيعات الصور السنوية',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                    ),
                  ),
                  3 => 
                  array (
                    'code' => '41020204',
                    'name_ar' => 'ايرادات مبيعات دفاتر الملاحظات',
                    'name_en' => 'ايرادات مبيعات دفاتر الملاحظات',
                    'type' => 'income',
                    'is_group' => true,
                    'children' => 
                    array (
                      0 => 
                      array (
                        'code' => '4102020401',
                        'name_ar' => 'دخل مبيعات دفاتر الملاحظات -  العام الحالي',
                        'name_en' => 'دخل مبيعات دفاتر الملاحظات -  العام الحالي',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                      1 => 
                      array (
                        'code' => '4102020402',
                        'name_ar' => 'استرداد مبيعات دفاتر الملاحظات -  العام الحالي',
                        'name_en' => 'استرداد مبيعات دفاتر الملاحظات -  العام الحالي',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                      2 => 
                      array (
                        'code' => '4102020403',
                        'name_ar' => 'دخل مبيعات دفاتر الملاحظات - السنوات الماضية',
                        'name_en' => 'دخل مبيعات دفاتر الملاحظات - السنوات الماضية',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                      3 => 
                      array (
                        'code' => '4102020404',
                        'name_ar' => 'استرداد مبيعات دفاتر الملاحظات - السنوات الماضية',
                        'name_en' => 'استرداد مبيعات دفاتر الملاحظات - السنوات الماضية',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                    ),
                  ),
                  4 => 
                  array (
                    'code' => '41020205',
                    'name_ar' => 'ايرادات مبيعات ادوات قرطاسية',
                    'name_en' => 'ايرادات مبيعات ادوات قرطاسية',
                    'type' => 'income',
                    'is_group' => true,
                    'children' => 
                    array (
                      0 => 
                      array (
                        'code' => '4102020501',
                        'name_ar' => 'دخل مبيعات مجموعة ادوات قرطاسية',
                        'name_en' => 'دخل مبيعات مجموعة ادوات قرطاسية',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                      1 => 
                      array (
                        'code' => '4102020502',
                        'name_ar' => 'استرداد مجموعة ادوات قرطاسية',
                        'name_en' => 'استرداد مجموعة ادوات قرطاسية',
                        'type' => 'income',
                        'is_group' => false,
                      ),
                    ),
                  ),
                ),
              ),
            ),
          ),
          2 => 
          array (
            'code' => '4103',
            'name_ar' => 'الايرادات اخرى',
            'name_en' => 'الايرادات اخرى',
            'type' => 'income',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '410301',
                'name_ar' => 'ايرادات اخرى',
                'name_en' => 'ايرادات اخرى',
                'type' => 'income',
                'is_group' => false,
              ),
            ),
          ),
        ),
      ),
    ),
  ),
  4 => 
  array (
    'code' => '5',
    'name_ar' => 'المصورفات',
    'name_en' => 'Expenses',
    'type' => 'expense',
    'is_group' => true,
    'children' => 
    array (
      0 => 
      array (
        'code' => '51',
        'name_ar' => 'المصروفات التشغيلية المباشرة',
        'name_en' => 'Direct Operational Expenses',
        'type' => 'expense',
        'is_group' => true,
        'children' => 
        array (
          0 => 
          array (
            'code' => '5101',
            'name_ar' => 'المصروفات الاكاديمية التعليمية',
            'name_en' => 'المصروفات الاكاديمية التعليمية',
            'type' => 'expense',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '510101',
                'name_ar' => 'مصروفات الكادر التعليمي',
                'name_en' => 'مصروفات الكادر التعليمي',
                'type' => 'expense',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '51010101',
                    'name_ar' => 'رواتب واجور الكادر التعليمي',
                    'name_en' => 'رواتب واجور الكادر التعليمي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '51010102',
                    'name_ar' => 'اضافي الكادر التعليمي',
                    'name_en' => 'اضافي الكادر التعليمي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  2 => 
                  array (
                    'code' => '51010103',
                    'name_ar' => 'مكافاة الكادر التعليمي',
                    'name_en' => 'مكافاة الكادر التعليمي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  3 => 
                  array (
                    'code' => '51010104',
                    'name_ar' => 'مصروفات بدل ايجار سكن الكادر التعليمي',
                    'name_en' => 'مصروفات بدل ايجار سكن الكادر التعليمي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  4 => 
                  array (
                    'code' => '51010105',
                    'name_ar' => 'مصروفات شقق للايجار في بيت الكادر التعليمي',
                    'name_en' => 'مصروفات شقق للايجار في بيت الكادر التعليمي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  5 => 
                  array (
                    'code' => '51010106',
                    'name_ar' => 'مصروفات اجازات الكادر التعليمي',
                    'name_en' => 'مصروفات اجازات الكادر التعليمي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  6 => 
                  array (
                    'code' => '51010107',
                    'name_ar' => 'مصروفات نهاية الخدمة الكادر التعليمي',
                    'name_en' => 'مصروفات نهاية الخدمة الكادر التعليمي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  7 => 
                  array (
                    'code' => '51010108',
                    'name_ar' => 'مصروفات تامينات اجتماعية الكادر التعليمي',
                    'name_en' => 'مصروفات تامينات اجتماعية الكادر التعليمي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  8 => 
                  array (
                    'code' => '51010109',
                    'name_ar' => 'مصروفات تذكرة طيران الكادر التعليمي',
                    'name_en' => 'مصروفات تذكرة طيران الكادر التعليمي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  9 => 
                  array (
                    'code' => '51010110',
                    'name_ar' => 'مصروفات التامين الطبي الكادر التعليمي',
                    'name_en' => 'مصروفات التامين الطبي الكادر التعليمي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  10 => 
                  array (
                    'code' => '51010111',
                    'name_ar' => 'مصروفات اقامات الكادر التعليمي',
                    'name_en' => 'مصروفات اقامات الكادر التعليمي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  11 => 
                  array (
                    'code' => '51010112',
                    'name_ar' => 'مصروفات دورات تدريبية للكادر التعليمي',
                    'name_en' => 'مصروفات دورات تدريبية للكادر التعليمي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  12 => 
                  array (
                    'code' => '51010113',
                    'name_ar' => 'مصروفات مدفوعات الكادر التعليمي  الاخرى',
                    'name_en' => 'مصروفات مدفوعات الكادر التعليمي  الاخرى',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  13 => 
                  array (
                    'code' => '51010114',
                    'name_ar' => 'مصروفات ملابس الكادر التعليمي',
                    'name_en' => 'مصروفات ملابس الكادر التعليمي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  14 => 
                  array (
                    'code' => '51010115',
                    'name_ar' => 'مصروفات بدل موبايل الكادر التعليمي',
                    'name_en' => 'مصروفات بدل موبايل الكادر التعليمي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  15 => 
                  array (
                    'code' => '51010116',
                    'name_ar' => 'مصروفات انتقالات الكادر التعليمي',
                    'name_en' => 'مصروفات انتقالات الكادر التعليمي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  16 => 
                  array (
                    'code' => '51010117',
                    'name_ar' => 'مصروفات انترنت  الكادر التعليمي',
                    'name_en' => 'مصروفات انترنت  الكادر التعليمي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  17 => 
                  array (
                    'code' => '51010118',
                    'name_ar' => 'مصروفات هواتف ونقال الحضانات',
                    'name_en' => 'مصروفات هواتف ونقال الحضانات',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  18 => 
                  array (
                    'code' => '51010119',
                    'name_ar' => 'مصروفات ضيافة بوفية الكادر التعليمي',
                    'name_en' => 'مصروفات ضيافة بوفية الكادر التعليمي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  19 => 
                  array (
                    'code' => '51010120',
                    'name_ar' => 'مصروفات متنوعة الكادر التعليمي',
                    'name_en' => 'مصروفات متنوعة الكادر التعليمي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  20 => 
                  array (
                    'code' => '51010121',
                    'name_ar' => 'مصروفات أدوات المطبخ الكادر التعليمي',
                    'name_en' => 'مصروفات أدوات المطبخ الكادر التعليمي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  21 => 
                  array (
                    'code' => '51010122',
                    'name_ar' => 'مصروفات رحلات الطيران الكادر التعليمي',
                    'name_en' => 'مصروفات رحلات الطيران الكادر التعليمي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  22 => 
                  array (
                    'code' => '51010123',
                    'name_ar' => 'مصروفات تشغيل الاجهزة والمعدات الكادر التعليمي',
                    'name_en' => 'مصروفات تشغيل الاجهزة والمعدات الكادر التعليمي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  23 => 
                  array (
                    'code' => '51010124',
                    'name_ar' => 'مصروفات صيانة واصلاح بيت الكادر التعليمي',
                    'name_en' => 'مصروفات صيانة واصلاح بيت الكادر التعليمي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  24 => 
                  array (
                    'code' => '51010125',
                    'name_ar' => 'مصروفات  استقدام العمالة واذون العمل الكادر التعليمي',
                    'name_en' => 'مصروفات  استقدام العمالة واذون العمل الكادر التعليمي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                ),
              ),
              1 => 
              array (
                'code' => '510102',
                'name_ar' => 'المصروفات الدراسية المباشرة',
                'name_en' => 'المصروفات الدراسية المباشرة',
                'type' => 'expense',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '51010201',
                    'name_ar' => 'مصروفات ضيافة وبوفية الحضانات',
                    'name_en' => 'مصروفات ضيافة وبوفية الحضانات',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '51010202',
                    'name_ar' => 'مصروفات  التطبيق والدعم',
                    'name_en' => 'مصروفات  التطبيق والدعم',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  2 => 
                  array (
                    'code' => '51010203',
                    'name_ar' => 'مصروفات آلة معطر الهواء',
                    'name_en' => 'مصروفات آلة معطر الهواء',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  3 => 
                  array (
                    'code' => '51010204',
                    'name_ar' => 'مصروفات طفايات الحريق',
                    'name_en' => 'مصروفات طفايات الحريق',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  4 => 
                  array (
                    'code' => '51010205',
                    'name_ar' => 'مصروفات  احبار',
                    'name_en' => 'مصروفات  احبار',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  5 => 
                  array (
                    'code' => '51010206',
                    'name_ar' => 'مصروفات اسبوع افتتاح المدرسة',
                    'name_en' => 'مصروفات اسبوع افتتاح المدرسة',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  6 => 
                  array (
                    'code' => '51010207',
                    'name_ar' => 'مصروفات نقل الطلاب',
                    'name_en' => 'مصروفات نقل الطلاب',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  7 => 
                  array (
                    'code' => '51010208',
                    'name_ar' => 'مصروفات مستلزمات انشطة وفعاليات الفصول الكادر التعليمي',
                    'name_en' => 'مصروفات مستلزمات انشطة وفعاليات الفصول الكادر التعليمي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  8 => 
                  array (
                    'code' => '51010209',
                    'name_ar' => 'مصروفات  مستلزمات العيادة المدرسية ولوازم رعاية الاطفال',
                    'name_en' => 'مصروفات  مستلزمات العيادة المدرسية ولوازم رعاية الاطفال',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                ),
              ),
              2 => 
              array (
                'code' => '510103',
                'name_ar' => 'مصروفات الانشطة و الفعاليات',
                'name_en' => 'مصروفات الانشطة و الفعاليات',
                'type' => 'expense',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '51010301',
                    'name_ar' => 'مصروفات الرحلات',
                    'name_en' => 'مصروفات الرحلات',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '51010302',
                    'name_ar' => 'مصروفات حفلة التخرج',
                    'name_en' => 'مصروفات حفلة التخرج',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  2 => 
                  array (
                    'code' => '51010303',
                    'name_ar' => 'مصروفات  المخيم الصيفي صباحي',
                    'name_en' => 'مصروفات  المخيم الصيفي صباحي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  3 => 
                  array (
                    'code' => '51010304',
                    'name_ar' => 'مصروفات  المخيم الصيفي مسائي',
                    'name_en' => 'مصروفات  المخيم الصيفي مسائي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  4 => 
                  array (
                    'code' => '51010305',
                    'name_ar' => 'مصروفات رواتب موظفين المخيم الصيفي',
                    'name_en' => 'مصروفات رواتب موظفين المخيم الصيفي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  5 => 
                  array (
                    'code' => '51010306',
                    'name_ar' => 'مصروفات  المخيم الشتوي صباحي',
                    'name_en' => 'مصروفات  المخيم الشتوي صباحي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  6 => 
                  array (
                    'code' => '51010307',
                    'name_ar' => 'مصروفات  المخيم الشتوي مسائي',
                    'name_en' => 'مصروفات  المخيم الشتوي مسائي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  7 => 
                  array (
                    'code' => '51010308',
                    'name_ar' => 'مصروفات رواتب موظفين المخيم الشتوي',
                    'name_en' => 'مصروفات رواتب موظفين المخيم الشتوي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  8 => 
                  array (
                    'code' => '51010309',
                    'name_ar' => 'مصروفات  حفلات اعياد الميلاد',
                    'name_en' => 'مصروفات  حفلات اعياد الميلاد',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  9 => 
                  array (
                    'code' => '51010310',
                    'name_ar' => 'مصروفات  مخيم رمضان',
                    'name_en' => 'مصروفات  مخيم رمضان',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  10 => 
                  array (
                    'code' => '51010311',
                    'name_ar' => 'مصروفات ايجار مدرسة النادي الصيفي',
                    'name_en' => 'مصروفات ايجار مدرسة النادي الصيفي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  11 => 
                  array (
                    'code' => '51010312',
                    'name_ar' => 'مصروفات نادي بعد المدرسة',
                    'name_en' => 'مصروفات نادي بعد المدرسة',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                ),
              ),
              3 => 
              array (
                'code' => '510104',
                'name_ar' => 'مصروفات اليونيفورم',
                'name_en' => 'مصروفات اليونيفورم',
                'type' => 'expense',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '51010401',
                    'name_ar' => 'مصروفات اليونيفور للعام الدراسي',
                    'name_en' => 'مصروفات اليونيفور للعام الدراسي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '51010402',
                    'name_ar' => 'مصروفات اليونيفور للنادى الصيفي',
                    'name_en' => 'مصروفات اليونيفور للنادى الصيفي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  2 => 
                  array (
                    'code' => '51010403',
                    'name_ar' => 'مصروفات اليونيفورم  للنادى الشتوي',
                    'name_en' => 'مصروفات اليونيفورم  للنادى الشتوي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                ),
              ),
              4 => 
              array (
                'code' => '510105',
                'name_ar' => 'مصروفات الكتب والدفاتر  واللوازم المدرسية',
                'name_en' => 'مصروفات الكتب والدفاتر  واللوازم المدرسية',
                'type' => 'expense',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '51010501',
                    'name_ar' => 'مصروفات كتب مدرسية  - ( عربي + انجليزي )',
                    'name_en' => 'مصروفات كتب مدرسية  - ( عربي + انجليزي )',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '51010502',
                    'name_ar' => 'مصروفات أدوات تعليمية',
                    'name_en' => 'مصروفات أدوات تعليمية',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  2 => 
                  array (
                    'code' => '51010503',
                    'name_ar' => 'مصروفات  قرطاسية الأطفال',
                    'name_en' => 'مصروفات  قرطاسية الأطفال',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  3 => 
                  array (
                    'code' => '51010504',
                    'name_ar' => 'مصروفات كتب وقصص ووسائل تعليمية',
                    'name_en' => 'مصروفات كتب وقصص ووسائل تعليمية',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  4 => 
                  array (
                    'code' => '51010505',
                    'name_ar' => 'مصروفات ألعاب تعليمية داخلية',
                    'name_en' => 'مصروفات ألعاب تعليمية داخلية',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  5 => 
                  array (
                    'code' => '51010506',
                    'name_ar' => 'مصروفات مستلزمات الفصول',
                    'name_en' => 'مصروفات مستلزمات الفصول',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  6 => 
                  array (
                    'code' => '51010507',
                    'name_ar' => 'مصروفات مواد فنية وأشغال يدوية',
                    'name_en' => 'مصروفات مواد فنية وأشغال يدوية',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                ),
              ),
              5 => 
              array (
                'code' => '510106',
                'name_ar' => 'تشغيل وصيانة الحضانات',
                'name_en' => 'تشغيل وصيانة الحضانات',
                'type' => 'expense',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '51010601',
                    'name_ar' => 'مصروفات كهرباء الحضانات',
                    'name_en' => 'مصروفات كهرباء الحضانات',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '51010602',
                    'name_ar' => 'مصروفات مياه الحضانات',
                    'name_en' => 'مصروفات مياه الحضانات',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  2 => 
                  array (
                    'code' => '51010603',
                    'name_ar' => 'مصروفات صيانة المبنى الحضانات',
                    'name_en' => 'مصروفات صيانة المبنى الحضانات',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  3 => 
                  array (
                    'code' => '51010604',
                    'name_ar' => 'مصروفات صيانة أجهزة التكييف الحضانات',
                    'name_en' => 'مصروفات صيانة أجهزة التكييف الحضانات',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  4 => 
                  array (
                    'code' => '51010605',
                    'name_ar' => 'مصروفات صيانة الألعاب الحضانات',
                    'name_en' => 'مصروفات صيانة الألعاب الحضانات',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  5 => 
                  array (
                    'code' => '51010606',
                    'name_ar' => 'مصروفات صيانة المعدات الحضانات',
                    'name_en' => 'مصروفات صيانة المعدات الحضانات',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  6 => 
                  array (
                    'code' => '51010607',
                    'name_ar' => 'مصروفات مستلزمات تشغيلية أخرى',
                    'name_en' => 'مصروفات مستلزمات تشغيلية أخرى',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                ),
              ),
              6 => 
              array (
                'code' => '510107',
                'name_ar' => 'النظافة والتعقيم للحضانات',
                'name_en' => 'النظافة والتعقيم للحضانات',
                'type' => 'expense',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '51010701',
                    'name_ar' => 'مصروفات مواد تنظيف للحضانات',
                    'name_en' => 'مصروفات مواد تنظيف للحضانات',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '51010702',
                    'name_ar' => 'مصروفات مكافحة الحشرات للحضانات',
                    'name_en' => 'مصروفات مكافحة الحشرات للحضانات',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                ),
              ),
              7 => 
              array (
                'code' => '510108',
                'name_ar' => 'مصروفات الايجار للحضانات',
                'name_en' => 'مصروفات الايجار للحضانات',
                'type' => 'expense',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '51010801',
                    'name_ar' => 'ايجار مقر الحضانات',
                    'name_en' => 'ايجار مقر الحضانات',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
      1 => 
      array (
        'code' => '52',
        'name_ar' => 'مصاريف ادارية وعمومية',
        'name_en' => 'Salaries & Employee Benefits',
        'type' => 'expense',
        'is_group' => true,
        'children' => 
        array (
          0 => 
          array (
            'code' => '5201',
            'name_ar' => 'مصاريف ادارية وعمومية - الادارة',
            'name_en' => 'مصاريف ادارية وعمومية - الادارة',
            'type' => 'expense',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '520101',
                'name_ar' => 'تكاليف الموظفين',
                'name_en' => 'تكاليف الموظفين',
                'type' => 'expense',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '52010101',
                    'name_ar' => 'رواتب واجور الكادر الاداري',
                    'name_en' => 'رواتب واجور الكادر الاداري',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '52010102',
                    'name_ar' => 'اضافي الكادر الاداري',
                    'name_en' => 'اضافي الكادر الاداري',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  2 => 
                  array (
                    'code' => '52010103',
                    'name_ar' => 'مكافاة الكادر الاداراي',
                    'name_en' => 'مكافاة الكادر الاداراي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  3 => 
                  array (
                    'code' => '52010104',
                    'name_ar' => 'اقامات الكادر الاداري',
                    'name_en' => 'اقامات الكادر الاداري',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  4 => 
                  array (
                    'code' => '52010105',
                    'name_ar' => 'مصروفات الاجازات',
                    'name_en' => 'مصروفات الاجازات',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  5 => 
                  array (
                    'code' => '52010106',
                    'name_ar' => 'مصروفات نهاية الخدمة',
                    'name_en' => 'مصروفات نهاية الخدمة',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  6 => 
                  array (
                    'code' => '52010107',
                    'name_ar' => 'التامينات الاجتماعية للموظفين',
                    'name_en' => 'التامينات الاجتماعية للموظفين',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                ),
              ),
              1 => 
              array (
                'code' => '520102',
                'name_ar' => 'مصروفات التسويق والدعاية والاعلان',
                'name_en' => 'مصروفات التسويق والدعاية والاعلان',
                'type' => 'expense',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '52010201',
                    'name_ar' => 'مصروفات الدعاية والاعلان',
                    'name_en' => 'مصروفات الدعاية والاعلان',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '52010202',
                    'name_ar' => 'مصروفات الهدايا',
                    'name_en' => 'مصروفات الهدايا',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  2 => 
                  array (
                    'code' => '52010203',
                    'name_ar' => 'مصروفات الاحتفالات للحضانات',
                    'name_en' => 'مصروفات الاحتفالات للحضانات',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  3 => 
                  array (
                    'code' => '52010204',
                    'name_ar' => 'مصروفات اجتماعات اولياء الامور',
                    'name_en' => 'مصروفات اجتماعات اولياء الامور',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  4 => 
                  array (
                    'code' => '52010205',
                    'name_ar' => 'مصروفات التصوير الفوتوغرافي',
                    'name_en' => 'مصروفات التصوير الفوتوغرافي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  5 => 
                  array (
                    'code' => '52010206',
                    'name_ar' => 'مصروفات استئجار قاعات فندقية',
                    'name_en' => 'مصروفات استئجار قاعات فندقية',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                ),
              ),
              2 => 
              array (
                'code' => '520103',
                'name_ar' => 'المصاريف الحكومية',
                'name_en' => 'المصاريف الحكومية',
                'type' => 'expense',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '52010301',
                    'name_ar' => 'مصاريف ترخيص ورسوم حكومية',
                    'name_en' => 'مصاريف ترخيص ورسوم حكومية',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '52010302',
                    'name_ar' => 'المخالفات البلدية',
                    'name_en' => 'المخالفات البلدية',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                ),
              ),
              3 => 
              array (
                'code' => '520104',
                'name_ar' => 'مصاريف وثيقة تامين تعويضات الحياة والعمال',
                'name_en' => 'مصاريف وثيقة تامين تعويضات الحياة والعمال',
                'type' => 'expense',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '52010401',
                    'name_ar' => 'وثيقة التامين على الحياة والعمل',
                    'name_en' => 'وثيقة التامين على الحياة والعمل',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '52010402',
                    'name_ar' => 'مصروفات تامين على الطلاب',
                    'name_en' => 'مصروفات تامين على الطلاب',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                ),
              ),
              4 => 
              array (
                'code' => '520105',
                'name_ar' => 'مصروفات استشارات و  اتعاب مهنية',
                'name_en' => 'مصروفات استشارات و  اتعاب مهنية',
                'type' => 'expense',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '52010501',
                    'name_ar' => 'مصروفات اتعاب مكتب المراجعة والتدقيق',
                    'name_en' => 'مصروفات اتعاب مكتب المراجعة والتدقيق',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '52010502',
                    'name_ar' => 'مصروفات اتعاب المحامي',
                    'name_en' => 'مصروفات اتعاب المحامي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  2 => 
                  array (
                    'code' => '52010503',
                    'name_ar' => 'مصروفات الاستشارات',
                    'name_en' => 'مصروفات الاستشارات',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  3 => 
                  array (
                    'code' => '52010504',
                    'name_ar' => 'مصروفات خدمات اشراف تربوي خارجي',
                    'name_en' => 'مصروفات خدمات اشراف تربوي خارجي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  4 => 
                  array (
                    'code' => '52010505',
                    'name_ar' => 'مصروفات عمولات دكاترة الاشراف',
                    'name_en' => 'مصروفات عمولات دكاترة الاشراف',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                ),
              ),
              5 => 
              array (
                'code' => '520106',
                'name_ar' => 'مصروفات اي كلاود و السيرفر ات',
                'name_en' => 'مصروفات اي كلاود و السيرفر ات',
                'type' => 'expense',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '52010601',
                    'name_ar' => 'مصروفات اي كلاود و السيرفرات',
                    'name_en' => 'مصروفات اي كلاود و السيرفرات',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                ),
              ),
              6 => 
              array (
                'code' => '520107',
                'name_ar' => 'مصروفات سنوات سابقة',
                'name_en' => 'مصروفات سنوات سابقة',
                'type' => 'expense',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '52010701',
                    'name_ar' => 'مصروفات سنوات سابقة',
                    'name_en' => 'مصروفات سنوات سابقة',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                ),
              ),
              7 => 
              array (
                'code' => '520108',
                'name_ar' => 'الخصم المسموح به (الممنوح)',
                'name_en' => 'الخصم المسموح به (الممنوح)',
                'type' => 'expense',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '52010801',
                    'name_ar' => 'الخصم المسموح به (الممنوح) لرسوم التسجيل',
                    'name_en' => 'الخصم المسموح به (الممنوح) لرسوم التسجيل',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '52010802',
                    'name_ar' => 'الخصم المسموح به (الممنوح) للرسوم الدراسية',
                    'name_en' => 'الخصم المسموح به (الممنوح) للرسوم الدراسية',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                ),
              ),
              8 => 
              array (
                'code' => '520109',
                'name_ar' => 'مصروفات  ادارية وعمومية',
                'name_en' => 'مصروفات  ادارية وعمومية',
                'type' => 'expense',
                'is_group' => true,
                'children' => 
                array (
                  0 => 
                  array (
                    'code' => '52010901',
                    'name_ar' => 'مصروفات ايجار المكتب الرئيسي',
                    'name_en' => 'مصروفات ايجار المكتب الرئيسي',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  1 => 
                  array (
                    'code' => '52010902',
                    'name_ar' => 'مصروفات تاجير سيارة',
                    'name_en' => 'مصروفات تاجير سيارة',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  2 => 
                  array (
                    'code' => '52010903',
                    'name_ar' => 'مصروفات الكهرباء والماء',
                    'name_en' => 'مصروفات الكهرباء والماء',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  3 => 
                  array (
                    'code' => '52010904',
                    'name_ar' => 'مصروفات اشتراكات هواتف ارضية',
                    'name_en' => 'مصروفات اشتراكات هواتف ارضية',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  4 => 
                  array (
                    'code' => '52010905',
                    'name_ar' => 'مصروفات هواتف ونقال',
                    'name_en' => 'مصروفات هواتف ونقال',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  5 => 
                  array (
                    'code' => '52010906',
                    'name_ar' => 'مصروفات انتر نت',
                    'name_en' => 'مصروفات انتر نت',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  6 => 
                  array (
                    'code' => '52010907',
                    'name_ar' => 'مصروفات النظافة',
                    'name_en' => 'مصروفات النظافة',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  7 => 
                  array (
                    'code' => '52010908',
                    'name_ar' => 'مصروفات صيانة المقر الاداري',
                    'name_en' => 'مصروفات صيانة المقر الاداري',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  8 => 
                  array (
                    'code' => '52010909',
                    'name_ar' => 'مصروفات غرامات المرور',
                    'name_en' => 'مصروفات غرامات المرور',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  9 => 
                  array (
                    'code' => '52010910',
                    'name_ar' => 'مصروفات القرطاسية',
                    'name_en' => 'مصروفات القرطاسية',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  10 => 
                  array (
                    'code' => '52010911',
                    'name_ar' => 'مصروفات طباعة وتصوير',
                    'name_en' => 'مصروفات طباعة وتصوير',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  11 => 
                  array (
                    'code' => '52010912',
                    'name_ar' => 'مصروفات الحاسوب',
                    'name_en' => 'مصروفات الحاسوب',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  12 => 
                  array (
                    'code' => '52010913',
                    'name_ar' => 'مصروفات متنوعة',
                    'name_en' => 'مصروفات متنوعة',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  13 => 
                  array (
                    'code' => '52010914',
                    'name_ar' => 'مصروفات رحلات العمل والسفر',
                    'name_en' => 'مصروفات رحلات العمل والسفر',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  14 => 
                  array (
                    'code' => '52010915',
                    'name_ar' => 'مصروفات بوفية وضيافة',
                    'name_en' => 'مصروفات بوفية وضيافة',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  15 => 
                  array (
                    'code' => '52010916',
                    'name_ar' => 'مصروفات صيانة البرامج والانترنت والسيرفرات',
                    'name_en' => 'مصروفات صيانة البرامج والانترنت والسيرفرات',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  16 => 
                  array (
                    'code' => '52010917',
                    'name_ar' => 'مصروفات احبار',
                    'name_en' => 'مصروفات احبار',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  17 => 
                  array (
                    'code' => '52010918',
                    'name_ar' => 'مصروفات مواقف سيارات',
                    'name_en' => 'مصروفات مواقف سيارات',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  18 => 
                  array (
                    'code' => '52010919',
                    'name_ar' => 'مصروفات انتقالات الادارة',
                    'name_en' => 'مصروفات انتقالات الادارة',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                  19 => 
                  array (
                    'code' => '52010920',
                    'name_ar' => 'مصروفات اكراميات',
                    'name_en' => 'مصروفات اكراميات',
                    'type' => 'expense',
                    'is_group' => false,
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
      2 => 
      array (
        'code' => '53',
        'name_ar' => 'مصروفات الاهلاك والاطفاء',
        'name_en' => 'Rent & Facility Expenses',
        'type' => 'expense',
        'is_group' => true,
        'children' => 
        array (
          0 => 
          array (
            'code' => '5301',
            'name_ar' => 'مصروفات الاهلاك للاصول الثابتة',
            'name_en' => 'مصروفات الاهلاك للاصول الثابتة',
            'type' => 'expense',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '530101',
                'name_ar' => 'مصروفات اهلاك اصول اثاث ومفروشات',
                'name_en' => 'مصروفات اهلاك اصول اثاث ومفروشات',
                'type' => 'expense',
                'is_group' => false,
              ),
              1 => 
              array (
                'code' => '530102',
                'name_ar' => 'مصروفات اهلاك اصول أجهزة ومعدات',
                'name_en' => 'مصروفات اهلاك اصول أجهزة ومعدات',
                'type' => 'expense',
                'is_group' => false,
              ),
              2 => 
              array (
                'code' => '530103',
                'name_ar' => 'مصروفات اهلاك اصول كمبيوترات وطابعات وأجهزة الكترونية',
                'name_en' => 'مصروفات اهلاك اصول كمبيوترات وطابعات وأجهزة الكترونية',
                'type' => 'expense',
                'is_group' => false,
              ),
              3 => 
              array (
                'code' => '530104',
                'name_ar' => 'مصروفات اهلاك اصول سيارت',
                'name_en' => 'مصروفات اهلاك اصول سيارت',
                'type' => 'expense',
                'is_group' => false,
              ),
              4 => 
              array (
                'code' => '530105',
                'name_ar' => 'مصروفات اهلاك أصول معدات تعليمية',
                'name_en' => 'مصروفات اهلاك أصول معدات تعليمية',
                'type' => 'expense',
                'is_group' => false,
              ),
              5 => 
              array (
                'code' => '530106',
                'name_ar' => 'مصروفات اهلاك اصول - تحسينات على العين الماجرة',
                'name_en' => 'مصروفات اهلاك اصول - تحسينات على العين الماجرة',
                'type' => 'expense',
                'is_group' => false,
              ),
            ),
          ),
          1 => 
          array (
            'code' => '5302',
            'name_ar' => 'مصروفات اطفاء الاصول الغير ملموسة',
            'name_en' => 'مصروفات اطفاء الاصول الغير ملموسة',
            'type' => 'expense',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '530201',
                'name_ar' => 'مصروفات اطفاء اصول غير ملموسة -  مواقع وبرامج كمبيوتر',
                'name_en' => 'مصروفات اطفاء اصول غير ملموسة -  مواقع وبرامج كمبيوتر',
                'type' => 'expense',
                'is_group' => false,
              ),
              1 => 
              array (
                'code' => '530202',
                'name_ar' => 'مصروفات اطفاء اصول غير ملموسة -  تكلفة الترخيص',
                'name_en' => 'مصروفات اطفاء اصول غير ملموسة -  تكلفة الترخيص',
                'type' => 'expense',
                'is_group' => false,
              ),
              2 => 
              array (
                'code' => '530203',
                'name_ar' => 'مصروفات اطفاء اصول غير ملموسة -  الشهرة',
                'name_en' => 'مصروفات اطفاء اصول غير ملموسة -  الشهرة',
                'type' => 'expense',
                'is_group' => false,
              ),
              3 => 
              array (
                'code' => '530204',
                'name_ar' => 'مصروفات اطفاء اصول غير ملموسة -  رخص النظام',
                'name_en' => 'مصروفات اطفاء اصول غير ملموسة -  رخص النظام',
                'type' => 'expense',
                'is_group' => false,
              ),
              4 => 
              array (
                'code' => '530205',
                'name_ar' => 'مصروفات اطفاء اصول غير ملموسة -  رخصة الحضانات',
                'name_en' => 'مصروفات اطفاء اصول غير ملموسة -  رخصة الحضانات',
                'type' => 'expense',
                'is_group' => false,
              ),
            ),
          ),
        ),
      ),
      3 => 
      array (
        'code' => '54',
        'name_ar' => 'أرباح وخسائر  اخري',
        'name_en' => 'General & Administrative Expenses',
        'type' => 'expense',
        'is_group' => true,
        'children' => 
        array (
          0 => 
          array (
            'code' => '5401',
            'name_ar' => 'أرباح وخسائر استبعاد الأصول',
            'name_en' => 'أرباح وخسائر استبعاد الأصول',
            'type' => 'expense',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '540101',
                'name_ar' => 'خسارة استبعاد الأصول',
                'name_en' => 'خسارة استبعاد الأصول',
                'type' => 'expense',
                'is_group' => false,
              ),
              1 => 
              array (
                'code' => '540102',
                'name_ar' => 'ربح استبعاد الأصول',
                'name_en' => 'ربح استبعاد الأصول',
                'type' => 'expense',
                'is_group' => false,
              ),
            ),
          ),
        ),
      ),
      4 => 
      array (
        'code' => '55',
        'name_ar' => 'مصروفات بنكية وعمولات',
        'name_en' => 'Bank Charges & Commission',
        'type' => 'expense',
        'is_group' => true,
        'children' => 
        array (
          0 => 
          array (
            'code' => '5501',
            'name_ar' => 'عمولات ورسوم بنكية',
            'name_en' => 'عمولات ورسوم بنكية',
            'type' => 'expense',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '550101',
                'name_ar' => 'عمولة رسوم تحصيل يوباي',
                'name_en' => 'عمولة رسوم تحصيل يوباي',
                'type' => 'expense',
                'is_group' => false,
                'reconcile' => true,
              ),
              1 => 
              array (
                'code' => '550102',
                'name_ar' => 'عمولات بنكية',
                'name_en' => 'عمولات بنكية',
                'type' => 'expense',
                'is_group' => false,
                'reconcile' => true,
              ),
            ),
          ),
        ),
      ),
      5 => 
      array (
        'code' => '56',
        'name_ar' => 'الديون المعدومة',
        'name_en' => 'Bad Debts',
        'type' => 'expense',
        'is_group' => true,
        'children' => 
        array (
          0 => 
          array (
            'code' => '5601',
            'name_ar' => 'الديون المعدومة',
            'name_en' => 'الديون المعدومة',
            'type' => 'expense',
            'is_group' => true,
            'children' => 
            array (
              0 => 
              array (
                'code' => '560101',
                'name_ar' => 'مصاريف الديون المعدومة',
                'name_en' => 'مصاريف الديون المعدومة',
                'type' => 'expense',
                'is_group' => false,
              ),
            ),
          ),
        ),
      ),
    ),
  ),
  5 => 
  array (
    'code' => '9',
    'name_ar' => 'حسابات وسيطة',
    'name_en' => 'Clearing & Suspense Accounts',
    'type' => 'off_balance',
    'is_group' => true,
    'children' => 
    array (
      0 => 
      array (
        'code' => '900003',
        'name_ar' => 'حساب وسيط القصور',
        'name_en' => 'حساب وسيط القصور',
        'type' => 'off_balance',
        'is_group' => false,
      ),
      1 => 
      array (
        'code' => '900004',
        'name_ar' => 'حساب وسيط ابو فطيرة',
        'name_en' => 'حساب وسيط ابو فطيرة',
        'type' => 'off_balance',
        'is_group' => false,
      ),
      2 => 
      array (
        'code' => '900005',
        'name_ar' => 'حساب وسيط الزهراء',
        'name_en' => 'حساب وسيط الزهراء',
        'type' => 'off_balance',
        'is_group' => false,
      ),
      3 => 
      array (
        'code' => '900006',
        'name_ar' => 'حساب وسيط ذمم موظفين',
        'name_en' => 'حساب وسيط ذمم موظفين',
        'type' => 'off_balance',
        'is_group' => false,
      ),
      4 => 
      array (
        'code' => '900007',
        'name_ar' => 'حساب وسيط تسوية حسابات الطلاب',
        'name_en' => 'حساب وسيط تسوية حسابات الطلاب',
        'type' => 'off_balance',
        'is_group' => false,
      ),
      5 => 
      array (
        'code' => '900009',
        'name_ar' => 'حساب وسيط ماي وورد',
        'name_en' => 'حساب وسيط ماي وورد',
        'type' => 'off_balance',
        'is_group' => false,
      ),
    ),
  ),
);
