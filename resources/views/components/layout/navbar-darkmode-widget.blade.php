{{-- Navbar darkmode widget --}}

<li class="nav-item adminlte-darkmode-widget">

    <a class="nav-link" href="#" role="button">
        <i class="{{ $makeIconClass() }}"></i>
    </a>

</li>

{{-- Add Javascript listener for the click event --}}

@once
@push('js')
<script>

    $(() => {

        const body = document.querySelector('body');
        const widget = document.querySelector('li.adminlte-darkmode-widget');
        const widgetIcon = widget.querySelector('i');

        // Get the set of classes to be toggled on the widget icon.

        const iconClasses = [
            ...@json($makeIconEnabledClass()),
            ...@json($makeIconDisabledClass())
        ];

        // Add 'click' event listener for the darkmode widget.

        widget.addEventListener('click', () => {

            // Toggle dark-mode class on the body tag.

            body.classList.toggle('dark-mode');

            // Toggle the classes on the widget icon.

            iconClasses.forEach((c) => widgetIcon.classList.toggle(c));

            // Notify the server. The server will be in charge to persist
            // the dark mode configuration over multiple request.

            const fetchCfg = {
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                method: 'POST',
            };

            fetch(
                "{{ route('adminlte.darkmode.toggle') }}",
                fetchCfg
            )
            .catch((error) => {
                console.log(
                    'Failed to notify server that dark mode was toggled',
                    error
                );
            });
        });
    })

</script>
@endpush
@endonce
