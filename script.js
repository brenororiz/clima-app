document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('weatherForm');
    const loading = document.getElementById('loading');

    form.addEventListener('submit', (event) => {
        event.preventDefault(); // Impede o envio padrão do formulário

        // Mostra o ícone de carregamento
        loading.classList.remove('hidden');

        // Simula um pequeno delay antes de enviar o formulário
        setTimeout(() => {
            form.submit();
        }, 1000);
    });
});