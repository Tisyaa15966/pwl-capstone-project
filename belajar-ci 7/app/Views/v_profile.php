<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Profil Information</h5>

        <div class="row mb-1">
            <div class="col-lg-3 col-md-4 label" style="color: #4154f1; font-weight: 600;">Username</div>
            <div class="col-lg-9 col-md-8" style="color: #000;">
                <?= session()->get('username') ?>
            </div>
        </div>

        <div class="row mb-1">
            <div class="col-lg-3 col-md-4 label" style="color: #4154f1; font-weight: 600;">Role</div>
            <div class="col-lg-9 col-md-8">
                <span class="badge bg-danger" style="color: #fff; font-weight: 500;">admin</span>
            </div>
        </div>

        <div class="row mb-1">
            <div class="col-lg-3 col-md-4 label" style="color: #4154f1; font-weight: 600;">Email</div>
            <div class="col-lg-9 col-md-8" style="color: #4154f1;">
                aprilexample@adm.dinus.ac.id </div>
        </div>

        <div class="row mb-1">
            <div class="col-lg-3 col-md-4 label" style="color: #4154f1; font-weight: 600;">Login Time</div>
            <div class="col-lg-9 col-md-8" style="color: #000;">
                <?= date('Y-m-d H:i:s', strtotime(session()->get('login_time') ?? 'now')) ?>
            </div>
        </div>

        <div class="row mb-1">
            <div class="col-lg-3 col-md-4 label" style="color: #4154f1; font-weight: 600;">Status</div>
            <div class="col-lg-9 col-md-8">
                <span class="badge bg-success" style="color: #fff;">
                    <i class="bi bi-check-circle"></i> Sudah Login
                </span>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>