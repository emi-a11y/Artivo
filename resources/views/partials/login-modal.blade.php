<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="loginForm">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="loginModalLabel">Log in to Artivo</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning py-2 small" role="alert">
                        Prototype only: this sign-in is simulated. Nothing is checked or stored.
                    </div>
                    <div class="mb-3">
                        <label for="loginEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="loginEmail" placeholder="name@example.com" required>
                    </div>
                    <div class="mb-1">
                        <label for="loginPassword" class="form-label">Password</label>
                        <input type="password" class="form-control" id="loginPassword" placeholder="Any password works" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Sign in (prototype)</button>
                </div>
            </form>
        </div>
    </div>
</div>
