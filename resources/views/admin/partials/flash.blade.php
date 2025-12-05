@php
    $flashMessage = session('success');
@endphp

<div class="flash-message-container">
    @if($flashMessage)
        <div class="flash-message flash-message--success" data-flash-message>
            <div class="flash-message__icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="flash-message__content">
                {{ $flashMessage }}
            </div>
            <button type="button" class="flash-message__close" aria-label="Close" data-close-flash>
                &times;
            </button>
        </div>
    @endif
</div>

