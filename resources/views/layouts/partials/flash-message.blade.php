{{-- resources/views/layouts/partials/flash-message.blade.php --}}

@if(session('success') || session('error') || session('warning') || session('info'))
    <div class="flash-container" id="flash-container">
        @if(session('success'))
            <div class="flash flash-success">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
                <button class="flash-close" onclick="this.parentElement.remove()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
        @if(session('error'))
            <div class="flash flash-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ session('error') }}</span>
                <button class="flash-close" onclick="this.parentElement.remove()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
        @if(session('warning'))
            <div class="flash flash-warning">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>{{ session('warning') }}</span>
                <button class="flash-close" onclick="this.parentElement.remove()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
        @if(session('info'))
            <div class="flash flash-info">
                <i class="fa-solid fa-circle-info"></i>
                <span>{{ session('info') }}</span>
                <button class="flash-close" onclick="this.parentElement.remove()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
    </div>
@endif

@if($errors->any())
    <div class="flash-container">
        <div class="flash flash-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
            <button class="flash-close" onclick="this.parentElement.remove()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>
@endif

<style>
    .flash-container {
        position: fixed;
        top: 80px;
        right: 20px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-width: 380px;
        width: 100%;
    }

    .flash {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 14px 16px;
        border-radius: var(--radius-md);
        font-size: 13.5px;
        font-weight: 500;
        box-shadow: var(--shadow-lg);
        animation: slideIn .25s ease;
        position: relative;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .flash i:first-child {
        font-size: 16px;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .flash>span,
    .flash>div {
        flex: 1;
        line-height: 1.5;
    }

    .flash-success {
        background: #D1FAE5;
        color: #065F46;
        border: 1px solid #6EE7B7;
    }

    .flash-error {
        background: #FEE2E2;
        color: #991B1B;
        border: 1px solid #FCA5A5;
    }

    .flash-warning {
        background: #FEF9C3;
        color: #854D0E;
        border: 1px solid #FDE047;
    }

    .flash-info {
        background: #DBEAFE;
        color: #1E40AF;
        border: 1px solid #93C5FD;
    }

    .flash-close {
        margin-left: auto;
        background: none;
        border: none;
        cursor: pointer;
        opacity: .6;
        font-size: 14px;
        flex-shrink: 0;
        color: inherit;
        padding: 0 2px;
    }

    .flash-close:hover {
        opacity: 1;
    }
</style>

<script>
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        document.querySelectorAll('.flash').forEach(el => {
            el.style.transition = 'opacity .4s ease';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        });
    }, 5000);
</script>