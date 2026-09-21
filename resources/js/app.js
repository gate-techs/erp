import './bootstrap';
/*
  Add custom scripts here
*/
import.meta.glob([
  '../assets/img/**',
  // '../assets/json/**',
  '../assets/vendor/fonts/**'
]);

import 'flatpickr/dist/flatpickr.css';
import flatpickr from 'flatpickr';
import { Arabic } from 'flatpickr/dist/l10n/ar.js';

// Flatpickr Initialization
document.addEventListener('DOMContentLoaded', function () {
  const initFlatpickr = () => {
    // Determine language safely
    const htmlLang = document.documentElement.lang || '';
    const isRtl = document.documentElement.dir === 'rtl' || htmlLang.toLowerCase().startsWith('ar');
    const currentLocale = isRtl ? Arabic : 'en';
    const currentPosition = isRtl ? 'auto right' : 'auto left';

    // Normal Date Pickers
    const dateInputs = document.querySelectorAll('.js-flatpickr-date:not(.flatpickr-input)');
    if (dateInputs.length > 0) {
      dateInputs.forEach(input => {
        if (!input._flatpickr) {
          flatpickr(input, {
            locale: currentLocale,
            position: currentPosition,
            dateFormat: 'Y-m-d',
            allowInput: true,
            altInput: false,
            disableMobile: true
          });
        }
      });
    }

    // Datetime Pickers
    const datetimeInputs = document.querySelectorAll('.js-flatpickr-datetime:not(.flatpickr-input)');
    if (datetimeInputs.length > 0) {
      datetimeInputs.forEach(input => {
        if (!input._flatpickr) {
          flatpickr(input, {
            locale: currentLocale,
            position: currentPosition,
            enableTime: true,
            dateFormat: 'Y-m-d\\TH:i',
            time_24hr: true,
            allowInput: true,
            altInput: false,
            disableMobile: true
          });
        }
      });
    }
  };

  initFlatpickr();

  // Handle dynamically added fields (e.g. repeaters)
  const observer = new MutationObserver((mutations) => {
    let shouldInit = false;
    mutations.forEach((mutation) => {
      if (mutation.addedNodes && mutation.addedNodes.length > 0) {
        mutation.addedNodes.forEach((node) => {
          if (node.nodeType === 1) {
            if (node.classList && (node.classList.contains('js-flatpickr-date') || node.classList.contains('js-flatpickr-datetime'))) {
              shouldInit = true;
            } else if (node.querySelectorAll && node.querySelectorAll('.js-flatpickr-date:not(.flatpickr-input), .js-flatpickr-datetime:not(.flatpickr-input)').length > 0) {
              shouldInit = true;
            }
          }
        });
      }
    });
    if (shouldInit) {
      initFlatpickr();
    }
  });

  observer.observe(document.body, { childList: true, subtree: true });
});
