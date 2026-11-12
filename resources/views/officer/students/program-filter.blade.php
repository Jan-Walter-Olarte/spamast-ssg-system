{{-- Shows only the programs of the chosen institute. Used by the filter bar and the form. --}}
<script>
    document.querySelectorAll('[data-institute-select]').forEach(function (instituteSelect) {
        var programSelect = document.querySelector(instituteSelect.dataset.target);

        function filterPrograms() {
            var institute = instituteSelect.value;

            Array.prototype.forEach.call(programSelect.options, function (option) {
                if (!option.value) { return; } // keep the placeholder option
                var show = !institute || option.dataset.institute === institute;
                option.hidden = !show;
                option.disabled = !show;
            });

            // If the selected program no longer matches, clear it.
            var current = programSelect.options[programSelect.selectedIndex];
            if (current && current.value && current.disabled) {
                programSelect.value = '';
            }
        }

        instituteSelect.addEventListener('change', filterPrograms);
        filterPrograms();
    });
</script>
