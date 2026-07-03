<!-- Welcome Modal -->
<div id="welcomeModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <button class="modal-close" onclick="closeModal()">&times;</button>
        
        <div class="modal-body">
            <h2 class="modal-title">Welcome Back!</h2>
            <p class="modal-subtitle">
                Hey <strong>{{ Auth::user()->name }}</strong>, glad to have you here. 👋
            </p>
            
            <div class="modal-message">
                <p>Explore our latest products and exclusive offers.</p>
            </div>
            
            <button class="btn-primary" onclick="closeModal()">
                Continue Shopping
            </button>
            
            <button class="btn-link" onclick="closeModal()">
                Maybe Later
            </button>
        </div>
    </div>
</div>

<style>
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .modal-content {
        background: white;
        border-radius: 8px;
        padding: 40px;
        max-width: 500px;
        width: 90%;
        position: relative;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-close {
        position: absolute;
        top: 15px;
        right: 15px;
        background: none;
        border: none;
        font-size: 28px;
        cursor: pointer;
        color: #999;
    }

    .modal-close:hover {
        color: #333;
    }

    .modal-title {
        font-size: 28px;
        font-weight: 700;
        color: #003366;
        margin: 0 0 10px 0;
        text-align: center;
    }

    .modal-subtitle {
        font-size: 16px;
        color: #666;
        text-align: center;
        margin: 0 0 20px 0;
    }

    .modal-message {
        background: #f5f5f5;
        padding: 15px;
        border-radius: 6px;
        margin: 20px 0;
        text-align: center;
        color: #555;
    }

    .btn-primary {
        width: 100%;
        padding: 12px 20px;
        background: #003366;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        margin-bottom: 10px;
        transition: background 0.3s;
    }

    .btn-primary:hover {
        background: #002244;
    }

    .btn-link {
        width: 100%;
        padding: 12px 20px;
        background: none;
        color: #003366;
        border: 1px solid #003366;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-link:hover {
        background: #f5f5f5;
    }
</style>

<script>
    function closeModal() {
        document.getElementById('welcomeModal').style.display = 'none';
    }

    // Close modal when clicking outside
    document.getElementById('welcomeModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>