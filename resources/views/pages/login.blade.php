@extends('layouts.bare')

@section('content')
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root">
        <!--begin::Authentication - Sign-in -->
        <div class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed"
            style="background-color: var(--primary-dny)">
            <!--begin::Content-->
            <div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20">
                <!--begin::Logo-->
                <a href="" class="mb-12 d-flex align-items-center justify-content-center gap-2">
                    <img alt="Logo" src="{{ asset('assets/images/Logo 50kb.jpg') }}" style="height: 12vh" />
                </a>
                <!--end::Logo-->
                <!--begin::Wrapper-->
                <div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
                    <!--begin::Form-->
                    <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form" method="POST"
                        action="{{ route('login') }}">
                        @csrf

                        <div class="text-center mb-5">
                            <h1 class="text-dark mb-3">Masuk Sistem</h1>
                        </div>

                        @if ($errors->has('login'))
                            <div class="alert alert-danger">
                                {{ $errors->first('login') }}
                            </div>
                        @endif

                        <div class="fv-row mb-5">

                            <label class="form-label fs-6 fw-bolder text-dark">Username</label>

                            <input class="form-control form-control-lg form-control-solid" type="text" name="username"
                                autocomplete="off" />

                        </div>

                        <div class="fv-row mb-5">

                            <div class="d-flex flex-stack mb-2">

                                <label class="form-label fw-bolder text-dark fs-6 mb-0">Password</label>

                            </div>

                            <input class="form-control form-control-lg form-control-solid" type="password" name="password"
                                autocomplete="off" />
                            <div class="d-flex flex-stack mb-2 justify-content-end" style="margin-top: 10px">
                                <a href="javascript:void(0)" onclick="beforeTambah()"
                                    class="link-primary fs-6 fw-bolder">Lupa
                                    Password?</a>
                            </div>
                        </div>
                        <div class="text-center">

                            <button type="submit" id="kt_sign_in_submit" class="btn btn-lg btn-primary w-100 mb-5">
                                <span class="indicator-label">Masuk</span>
                                {{-- <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span> --}}
                            </button>
                        </div>
                    </form>
                </div>

            </div>

        </div>
        <!--end::Authentication - Sign-in-->

        <div class="modal fade" id="kt_modal_invite_friends" tabindex="-1" aria-hidden="true">
            <!--begin::Modal dialog-->
            <div class="modal-dialog mw-650px">
                <!--begin::Modal content-->
                <div class="modal-content">
                    <!--begin::Modal header-->
                    <div class="modal-header pb-0 border-0 justify-content-end">
                        <!--begin::Close-->
                        <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                            <span class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                        transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"
                                        fill="currentColor" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </div>
                        <!--end::Close-->
                    </div>
                    <!--begin::Modal header-->
                    <!--begin::Modal body-->
                    <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
                        <!--begin::Heading-->
                        <div class="text-center mb-13">

                            <h1 class="mb-3">Lupa Password</h1>

                        </div>

                        <form action="" id="form_modal_spupplier">

                            <input type="hidden" name="mode" value="tambah" id="mode">
                            <input type="hidden" name="supl_no" value="" id="supl_no">

                            <div class="mb-10">
                                <div class="d-flex flex-column mb-2 fv-row">
                                    <!--begin::Label-->
                                    <label class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                                        <span class="required">Username</span>
                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                            title="Username"></i>
                                    </label>
                                    <!--end::Label-->
                                    <input type="text" class="form-control form-control-solid fs-6" placeholder="........."
                                        name="username" id="username" value="" required autocomplete="off" />
                                </div>
                            </div>

                            <div class="mb-10">
                                <div class="d-flex flex-column mb-7 fv-row">
                                    <!--begin::Label-->
                                    <label class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                                        <span class="required">Password Baru</span>
                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                            title=""></i>
                                    </label>
                                    <!--end::Label-->
                                    <input type="password" class="form-control form-control-solid" placeholder=""
                                        name="password" id="password" value="" required />
                                </div>
                            </div>

                        </form>


                        <div class="d-flex flex-stack">
                            <!--begin::Label-->
                            <div class="me-5 fw-bold">
                                <label class="fs-6">Pastikan Data yang diinput sudah benar</label>
                                <div class="fs-7 text-muted">Jika terjadi kesalahan silahkan menghubungi Team IT</div>
                            </div>
                            <!--end::Label-->
                            <!--begin::Switch-->
                            <button class="btn btn-sm btn-light btn-active-primary" onclick="forgotPassword()">
                                Simpan Data
                            </button>
                            <!--end::Switch-->
                        </div>
                        <!--end::Notice-->
                    </div>
                    <!--end::Modal body-->
                </div>
                <!--end::Modal content-->
            </div>
            <!--end::Modal dialog-->
        </div>

    </div>
    <!--end::Root-->
@endsection