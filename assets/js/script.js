$(document).ready(function() {
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Validação do formulário do hero
    $('#heroForm').on('submit', function(e) {
        e.preventDefault();
        
        $('#heroSuccessMessage').addClass('d-none');
        $('#heroErrorMessage').addClass('d-none');
        
        const form = this;
        if (!form.checkValidity()) {
            e.stopPropagation();
            $(form).addClass('was-validated');
            return;
        }
        
        const formData = {
            nome: $('input[name="nome"]').val(),
            estado: $('input[name="estado"]').val(),
            telefone: $('input[name="telefone"]').val(),
            email: $('input[name="email"]').val(),
            valor: $('input[name="valor"]').val(),
            processo: $('input[name="processo"]').val(),
            origem: 'hero_form'
        };
        
        // Enviar via AJAX
        $.ajax({
            url: 'mail/mail.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Mostrar mensagem de sucesso
                    $('#heroForm')[0].reset();
                    $('#heroForm').removeClass('was-validated');
                    
                    const modalHtml = `
                        <div class="modal fade" id="successModal" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header border-0">
                                        <h5 class="modal-title text-success">Sucesso!</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-center py-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#198754" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-3">
                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                            <polyline points="22 4 12 14.01 9 11.01"/>
                                        </svg>
                                        <h4 class="mb-3">Mensagem enviada com sucesso!</h4>
                                        <p class="text-muted">Entraremos em contato em breve.</p>
                                    </div>
                                    <div class="modal-footer border-0 justify-content-center">
                                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    $('body').append(modalHtml);
                    
                    // Mostrar modal
                    const modal = new bootstrap.Modal(document.getElementById('successModal'));
                    modal.show();
                    
                    // Remover modal após fechar
                    $('#successModal').on('hidden.bs.modal', function () {
                        $(this).remove();
                    });
                } else {
                    const errorHtml = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            ${response.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                    $('#heroForm').prepend(errorHtml);
                }
            },
            error: function(xhr, status, error) {
                const errorHtml = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Erro ao enviar mensagem. Por favor, tente novamente.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                $('#heroForm').prepend(errorHtml);
            }
        });
    });
    
    // Máscara para telefone no hero form
    $('#heroTelefone').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length > 10) {
            value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
        } else if (value.length > 5) {
            value = value.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
        } else if (value.length > 2) {
            value = value.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
        } else {
            value = value.replace(/^(\d*)/, '($1');
        }
        $(this).val(value);
    });
    
    // Máscara para valor monetário
    $('input[name="valor"]').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        value = (value/100).toFixed(2) + '';
        value = value.replace(".", ",");
        value = value.replace(/(\d)(\d{3})(\d{3}),/g, "$1.$2.$3,");
        value = value.replace(/(\d)(\d{3}),/g, "$1.$2,");
        $(this).val('R$ ' + value);
    });
    
    // Smooth scroll para âncoras
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = $(this.getAttribute('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 80
            }, 800);
        }
    });
    
    // Navbar scroll effect
    $(window).on('scroll', function() {
        if ($(window).scrollTop() > 100) {
            $('.navbar').addClass('navbar-scrolled');
        } else {
            $('.navbar').removeClass('navbar-scrolled');
        }
    });
    
    // Animação de elementos ao rolar
    function animateOnScroll() {
        const elements = $('.animate-on-scroll');
        elements.each(function() {
            const element = $(this);
            const position = element.offset().top;
            const scrollPosition = $(window).scrollTop() + $(window).height();
            
            if (scrollPosition > position + 100) {
                element.addClass('animated');
            }
        });
    }
    
    $(window).on('scroll', animateOnScroll);
    animateOnScroll();
    
    // Formatar número do processo
    $('input[name="processo"]').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length > 20) {
            value = value.substring(0, 20);
        }
        
        // Formatar como número de processo comum no Brasil
        if (value.length > 7) {
            value = value.replace(/^(\d{7})(\d{2})(\d{4})(\d{1})(\d{2})(\d{4}).*/, '$1-$2.$3.$4.$5.$6');
        } else if (value.length > 5) {
            value = value.replace(/^(\d{7})/, '$1-');
        }
        $(this).val(value);
    });
    
    // Validação de e-mail em tempo real
    $('input[type="email"]').on('blur', function() {
        const email = $(this).val();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (email && !emailRegex.test(email)) {
            $(this).addClass('is-invalid');
            $(this).next('.invalid-feedback').text('Por favor, insira um e-mail válido.');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    
    //checkbox de privacidade
    $('#heroPrivacy').on('change', function() {
        if ($(this).is(':checked')) {
            $(this).removeClass('is-invalid');
        }
    });
});