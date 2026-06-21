(() => {
    const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];
    const NUMBER_NAMES = [
        'amount',
        'price',
        'capacity',
        'quantity',
        'deposit',
        'rent',
        'score',
        'point'
    ];

    function labelOf(field) {
        const explicit = field.closest('.field-group')?.querySelector('label')?.textContent
            || field.parentElement?.querySelector('label')?.textContent
            || field.getAttribute('aria-label')
            || field.getAttribute('placeholder')
            || field.name
            || 'Trường này';

        return explicit.replace(/\s+/g, ' ').trim();
    }

    function isVisibleField(field) {
        if (field.type === 'hidden' || field.disabled) {
            return false;
        }

        if (field.closest('[hidden], .payment-hidden')) {
            return false;
        }

        const style = window.getComputedStyle(field);
        if (style.display === 'none' || style.visibility === 'hidden') {
            return false;
        }

        const holder = field.closest('.field-group, section, div');
        if (holder) {
            const holderStyle = window.getComputedStyle(holder);
            if (holderStyle.display === 'none' || holderStyle.visibility === 'hidden') {
                return false;
            }
        }

        return true;
    }

    function clearError(field) {
        field.classList.remove('is-invalid');
        const holder = field.closest('.field-group') || field.parentElement;
        const error = holder?.querySelector(`.field-error[data-for="${field.name}"]`);

        if (error) {
            error.remove();
        }
    }

    function showError(field, message) {
        clearError(field);
        field.classList.add('is-invalid');

        const holder = field.closest('.field-group') || field.parentElement;
        const error = document.createElement('div');
        error.className = 'field-error';
        error.dataset.for = field.name || '';
        error.textContent = message;

        if (holder) {
            holder.appendChild(error);
        }
    }

    function valueOf(field) {
        return (field.value || '').trim();
    }

    function isNumberField(field) {
        const name = (field.name || '').toLowerCase();
        return field.type === 'number' || NUMBER_NAMES.some((token) => name.includes(token));
    }

    function validateField(field) {
        if (!isVisibleField(field)) {
            return true;
        }

        clearError(field);

        const value = valueOf(field);
        const label = labelOf(field);

        if (field.required && value === '') {
            showError(field, `${label} không được để trống.`);
            return false;
        }

        if (value !== '' && (field.type === 'email' || field.name === 'email')) {
            const validEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);

            if (!validEmail) {
                showError(field, 'Email không đúng định dạng.');
                return false;
            }
        }

        if (value !== '' && field.name === 'password' && value.length < 6) {
            showError(field, 'Mật khẩu phải có ít nhất 6 ký tự.');
            return false;
        }

        if (value !== '' && isNumberField(field)) {
            const numericValue = Number(value);

            if (Number.isNaN(numericValue)) {
                showError(field, `${label} phải là số hợp lệ.`);
                return false;
            }

            if (numericValue < 0) {
                showError(field, `${label} không được âm.`);
                return false;
            }
        }

        if (field.type === 'file' && field.required && field.files.length === 0) {
            showError(field, `${label} không được để trống.`);
            return false;
        }

        if (field.type === 'file' && field.files.length > 0) {
            const invalidFile = Array.from(field.files).find((file) => {
                const extension = file.name.split('.').pop()?.toLowerCase();
                return !IMAGE_EXTENSIONS.includes(extension || '');
            });

            if (invalidFile) {
                showError(field, 'Ảnh minh chứng chỉ chấp nhận JPG, JPEG, PNG hoặc WEBP.');
                return false;
            }
        }

        return true;
    }

    function validatePasswordConfirmation(form) {
        const password = form.querySelector('[name="password"], [name="new_password"]');
        const confirm = form.querySelector('[name="confirm_password"], [name="confirm_new_password"]');

        if (!password || !confirm || !isVisibleField(confirm)) {
            return true;
        }

        if (valueOf(password) !== valueOf(confirm)) {
            showError(confirm, 'Mật khẩu xác nhận phải khớp.');
            return false;
        }

        return true;
    }

    function validateRouteRules(form) {
        const route = new URLSearchParams(window.location.search).get('route') || '';
        let valid = true;

        if (route === 'student/register-room') {
            valid = requireNamedField(form, 'semester_id', 'Vui lòng chọn học kỳ.') && valid;
            valid = requireNamedField(form, 'desired_room_type', 'Vui lòng chọn loại phòng.') && valid;
        }

        if (route === 'student/payment-submit') {
            const method = form.querySelector('[name="payment_method"]:checked')?.value || 'bank_transfer';

            if (method === 'bank_transfer') {
                valid = requireNamedField(form, 'sender_bank', 'Vui lòng nhập ngân hàng chuyển.') && valid;
                valid = requireNamedField(form, 'sender_account_name', 'Vui lòng nhập tên chủ tài khoản.') && valid;
                valid = requireNamedField(form, 'transaction_reference', 'Vui lòng nhập mã giao dịch.') && valid;
                valid = requireNamedField(form, 'payment_proof_image', 'Vui lòng upload ảnh bill chuyển khoản.') && valid;
            }
        }

        if (route === 'student/maintenance') {
            valid = requireNamedField(form, 'title', 'Vui lòng nhập tiêu đề sự cố.') && valid;
            valid = requireNamedField(form, 'description', 'Vui lòng nhập mô tả sự cố.') && valid;
            valid = requireNamedField(form, 'priority', 'Vui lòng chọn mức ưu tiên.') && valid;
        }

        return valid;
    }

    function requireNamedField(form, name, message) {
        const field = form.querySelector(`[name="${name}"]`);

        if (!field || !isVisibleField(field)) {
            return true;
        }

        if (field.type === 'file') {
            if (field.files && field.files.length > 0) {
                return true;
            }
        } else if (valueOf(field) !== '') {
            return true;
        }

        showError(field, message);
        return false;
    }

    function validateForm(form) {
        const fields = Array.from(form.querySelectorAll('input, select, textarea'));
        let valid = true;

        fields.forEach((field) => {
            valid = validateField(field) && valid;
        });

        valid = validatePasswordConfirmation(form) && valid;
        valid = validateRouteRules(form) && valid;

        if (!valid) {
            const firstError = form.querySelector('.is-invalid');
            firstError?.focus({ preventScroll: true });
            firstError?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        return valid;
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('form').forEach((form) => {
            form.noValidate = true;

            form.addEventListener('submit', (event) => {
                if (!validateForm(form)) {
                    event.preventDefault();
                }
            });

            form.addEventListener('input', (event) => {
                const field = event.target;

                if (field instanceof HTMLInputElement || field instanceof HTMLSelectElement || field instanceof HTMLTextAreaElement) {
                    validateField(field);
                }
            });

            form.addEventListener('change', (event) => {
                const field = event.target;

                if (field instanceof HTMLInputElement || field instanceof HTMLSelectElement || field instanceof HTMLTextAreaElement) {
                    validateField(field);
                }
            });
        });
    });
})();
