function markAll(status) {
    const radios = document.querySelectorAll(
        'input[name^="attendance"]'
    );

    radios.forEach(function (radio) {
        if (radio.value === status) {
            radio.checked = true;
        }
    });
}

function confirmDelete() {
    return confirm("Are you sure you want to delete this student?");
}