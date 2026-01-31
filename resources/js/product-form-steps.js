document.addEventListener('DOMContentLoaded', () => {
    const steps = Array.from(document.querySelectorAll('.form-step'));
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');

    let currentStep = 0;

    function showStep(step) {
        steps.forEach((s, i) => {
            s.classList.toggle('hidden', i !== step);
        });

        prevBtn.classList.toggle('hidden', step === 0);
        nextBtn.classList.toggle('hidden', step === steps.length - 1);
        submitBtn.classList.toggle('hidden', step !== steps.length - 1);
    }

    nextBtn.addEventListener('click', () => {
        if (currentStep < steps.length - 1) {
            currentStep++;
            showStep(currentStep);
        }
    });

    prevBtn.addEventListener('click', () => {
        if (currentStep > 0) {
            currentStep--;
            showStep(currentStep);
        }
    });

    showStep(currentStep);
});
