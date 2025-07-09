        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.style.transform = 'scale(1)';
            });
        });

        // Validación en tiempo real
        document.querySelector('input[name="password_confirmation"]').addEventListener('input', function() {
            const password = document.querySelector('input[name="password"]').value;
            const confirmPassword = this.value;
            
            if (password && confirmPassword && password !== confirmPassword) {
                this.style.borderColor = '#e74c3c';
            } else if (password && confirmPassword && password === confirmPassword) {
                this.style.borderColor = '#27ae60';
            } else {
                this.style.borderColor = '#e1e5e9';
            }
        });