<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-6">
        <?= form_open('buy', 'class="row g-3"') ?>

<?= form_hidden('username', session()->get('username')) ?>
<?= form_hidden('total_harga', [
    'value' => (string)($total ?? 0), 
    'id'    => 'total_harga'
]) ?>

<div class="col-12">
    <?= form_label('Nama', 'nama', ['class' => 'form-label']) ?>
    <?= form_input([
        'name'     => 'nama',
        'id'       => 'nama',
        'class'    => 'form-control',
        'value'    => session()->get('username'),
        'readonly' => true]) ?>
</div>
<div class="col-12">
    <?= form_label('Alamat', 'alamat', ['class' => 'form-label']) ?>
    <?= form_input([
        'name'  => 'alamat',
        'id'    => 'alamat',
        'class' => 'form-control']) ?>
</div> 
<div class="col-12"> 
    <?= form_label('Kelurahan', 'kelurahan', ['class' => 'form-label']) ?>
    <?= form_dropdown('kelurahan', [], '', ['id' => 'kelurahan', 'class' => 'form-control']) ?>
</div>
<div class="col-12"> 
    <?= form_label('Layanan', 'layanan', ['class' => 'form-label']) ?> 
    <?= form_dropdown('layanan', [], '', ['id' => 'layanan', 'class' => 'form-control']) ?>
</div>
<div class="col-12">
    <?= form_label('Ongkir', 'ongkir', ['class' => 'form-label']) ?>
    <?= form_input([
        'name'     => 'ongkir',
        'id'       => 'ongkir',
        'class'    => 'form-control',
        'readonly' => true]) ?>
</div>

<div class="col-12">
    <?= form_label('Kode Kupon', 'kupon_code', ['class' => 'form-label']) ?>
    <?= form_input([
        'name' => 'kupon_code',
        'id' => 'kupon_code',
        'class' => 'form-control',
        'placeholder' => 'Contoh: HEMAT20'
    ]) ?>
</div>

<div class="col-12">
    <?= form_submit(
        'submit',
        'Buat Pesanan',
        ['class' => 'btn btn-primary']) ?>
</div>

<?= form_close() ?> 
    </div>
   <div class="col-lg-6">

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <h4 class="fw-bold mb-4">
                Ringkasan Pesanan
            </h4>

            <table class="table align-middle">

                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Sub Total</th>
                    </tr>
                </thead>

                <tbody>

                <?php
                if (!empty($items)) :
                    foreach ($items as $item) :
                ?>

                    <tr>
                        <td><?= $item['name'] ?></td>
                        <td><?= number_to_currency($item['price'], 'IDR') ?></td>
                        <td><?= $item['qty'] ?></td>
                        <td><?= number_to_currency($item['price'] * $item['qty'], 'IDR') ?></td>
                    </tr>

                <?php
                    endforeach;
                endif;
                ?>

                <tr>
                    <td colspan="2"></td>
                    <td>Subtotal</td>
                    <td>
                        <span id="subtotal">
                            <?= number_to_currency($total,'IDR') ?>
                        </span>
                    </td>
                </tr>

                <tr>
                    <td colspan="2"></td>
                    <td class="text-danger">
                        Diskon Kupon
                    </td>
                    <td>
                        <span id="diskon" class="text-danger fw-bold">
                            Rp 0
                        </span>
                    </td>
                </tr>

                <tr>
                    <td colspan="2"></td>
                    <td>PPN (12%)</td>
                    <td>
                        <span id="ppn">
                            Rp 0
                        </span>
                    </td>
                </tr>

                <tr>
                    <td colspan="2"></td>
                    <td>Biaya Admin</td>
                    <td>
                        <span id="admin">
                            Rp 0
                        </span>
                    </td>
                </tr>

                <tr>
                    <td colspan="2"></td>
                    <td class="text-success">
                        <b>Subtotal</b><br>
                        <small>(+PPN + Admin - Kupon)</small>
                    </td>
                    <td>
                        <b>
                            <span id="subtotal2">
                                Rp 0
                            </span>
                        </b>
                    </td>
                </tr>

                <tr>
                    <td colspan="2"></td>
                    <td>Ongkir</td>
                    <td>
                        <span id="ongkir_view">
                            Rp 0
                        </span>
                    </td>
                </tr>

                <tr class="table-primary">

                    <td colspan="2"></td>

                    <td>
                        <b>
                            Grand Total
                            <br>
                            <small>(incl. Ongkir)</small>
                        </b>
                    </td>

                    <td>
                        <b>
                            <span id="total">
                                <?= number_to_currency($total,'IDR') ?>
                            </span>
                        </b>
                    </td>

                </tr>

                </tbody>

            </table>

        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('script') ?>
<script>
$(document).ready(function() {
    let ongkir = 0;
    let subtotal = <?= $total ?>;
    hitungTotal();

    function hitungTotal(){

        let kode=$("#kupon_code").val().toUpperCase();

        let diskon=0;

        if(kode=="HEMAT20")
            diskon=subtotal*0.20;

        else if(kode=="HEMAT30")
            diskon=subtotal*0.30;

        else if(kode=="MEMBER25")
            diskon=subtotal*0.25;

        let ppn=subtotal*0.12;

        let admin=0;

        if(subtotal<=15000000)
            admin=subtotal*0.005;

        else if(subtotal<=35000000)
            admin=subtotal*0.007;

        else
            admin=subtotal*0.009;

        let grand=subtotal-diskon+ppn+admin+ongkir;

        $("#ongkir").val(ongkir);

        $("#ongkir_view").text("Rp "+ongkir.toLocaleString('id-ID'));

        $("#diskon").text("- Rp "+diskon.toLocaleString('id-ID'));

        $("#ppn").text("Rp "+ppn.toLocaleString('id-ID'));

        $("#admin").text("Rp " + admin.toLocaleString('id-ID'));

        let subtotalSetelahDiskon = subtotal - diskon + ppn + admin;

        $("#subtotal2").text("Rp " + subtotalSetelahDiskon.toLocaleString('id-ID'));

        $("#total").text("Rp " + grand.toLocaleString('id-ID'));

        $("#total_harga").val(grand);

    }

    $('#kelurahan').select2({
        placeholder: 'Cari daerah tujuan',
        minimumInputLength: 3, 
        ajax: {
            url: '<?= site_url('ajax/destinations') ?>',
            dataType: 'json',
            delay: 300,
            data: function(params) {
                return {
                    q: params.term
                };
            },
            processResults: function(data) {
                return data;
            },
            cache: true
        }
     });

        $("#kelurahan").on('change', function () {
        let id_kelurahan = $(this).val();

        $("#layanan").empty();
        ongkir = 0;
        hitungTotal(); 

        $.ajax({
            url: "<?= site_url('ajax/costs') ?>", 
            dataType: "json",
            data: {
                destination: id_kelurahan
            },
            success: function (data) { 
                data.forEach(function (item) {
                    $("#layanan").append(
                        $('<option>', {
                            value: item.cost,
                            text: `${item.description} (${item.service}) : estimasi ${item.etd}`
                        })
                    );
                });
            }
        });
    });
    $("#layanan").on('change', function() {
        ongkir = parseInt($(this).val());
        hitungTotal();
    }); 
    $("#kupon_code").on('keyup change', function() {
    hitungTotal();
    });
});
</script>
<?= $this->endSection() ?>