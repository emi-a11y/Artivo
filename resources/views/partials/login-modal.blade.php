@php
    $openTab = $errors->register->any() ? 'register' : (($errors->login->any() || session('openLogin')) ? 'login' : '');
@endphp
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true" data-open-tab="{{ $openTab }}">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="loginModalLabel">Welcome to Artivo</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-pills artivo-modal-tabs mb-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-login" data-bs-toggle="pill" data-bs-target="#pane-login" type="button" role="tab" aria-controls="pane-login" aria-selected="true">Log in</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-register" data-bs-toggle="pill" data-bs-target="#pane-register" type="button" role="tab" aria-controls="pane-register" aria-selected="false">Create account</button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="pane-login" role="tabpanel" aria-labelledby="tab-login">
                        <form action="{{ route('login.attempt') }}" method="post" novalidate>
                            @csrf
                            @if ($errors->login->any())
                                <div class="alert alert-danger py-2 small" role="alert">{{ $errors->login->first() }}</div>
                            @endif
                            <div class="mb-3">
                                <label for="loginEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="loginEmail" name="email" value="{{ old('email') }}" placeholder="name@example.com" required>
                            </div>
                            <div class="mb-3">
                                <label for="loginPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="loginPassword" name="password" required>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="loginRemember" name="remember" value="1">
                                <label class="form-check-label" for="loginRemember">Remember me</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Log in</button>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="pane-register" role="tabpanel" aria-labelledby="tab-register">
                        <form action="{{ route('register') }}" method="post" novalidate>
                            @csrf
                            @if ($errors->register->any())
                                <div class="alert alert-danger py-2 small" role="alert">
                                    @foreach ($errors->register->all() as $message)<div>{{ $message }}</div>@endforeach
                                </div>
                            @endif
                            <div class="mb-3">
                                <label for="regName" class="form-label">Name</label>
                                <input type="text" class="form-control" id="regName" name="name" value="{{ old('name') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="regEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="regEmail" name="email" value="{{ old('email') }}" required>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-sm-6">
                                    <label for="regPassword" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="regPassword" name="password" minlength="6" required>
                                </div>
                                <div class="col-sm-6">
                                    <label for="regPassword2" class="form-label">Confirm</label>
                                    <input type="password" class="form-control" id="regPassword2" name="password_confirmation" minlength="6" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Create account</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
