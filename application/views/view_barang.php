<!DOCTYPE html>
<html lang="en">
<head>
    <title>(CRUD) Ajax Ignited Datatable | SimpleCodz</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="<?=base_url();?>assets/bootstrap-4/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/DataTables-1.10.18/css/dataTables.bootstrap4.min.css">
</head>
<body>

    <div class="container pt-4">
        <h1 class="h3 text-center">CodeIgniter Ajax CRUD using Jquery with Ignited Datatables Server-side</h1>
        <p class="small text-center">by <a href="https://simplecodz.blogspot.com">SimpleCodz</a></p>
    
        <div class="mt-5 mb-4">
            <h4 class="card-title text-center">Data Barang</h4>
            <button class="btn btn-sm btn-primary" onclick="add_barang()"><i class="fa fa-plus"></i> Tambah Barang</button>
            <button class="btn btn-sm btn-secondary" onclick="reload_ajax()"><i class="fa fa-refresh"></i> Reload</button>
        </div>

        <table id="barang" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Barang</th>
                    <th>Stok</th>
                    <th>Kategori</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
            <tfoot>
                <tr>
                    <th>No.</th>
                    <th>Nama Barang</th>
                    <th>Stok</th>
                    <th>Kategori</th>
                    <th>Action</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Bootstrap modal -->
    <div class="modal fade" id="modal_form" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_title">Barang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body form">
                    <form action="#" id="form">
                        <input type="hidden" value="" name="id"/> 
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label">Nama Barang</label>
                                <input name="nama_barang" class="form-control" type="text">
                                <span class="invalid-feedback"></span>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Stok</label>
                                <input name="stok" class="form-control" type="text">
                                <span class="invalid-feedback"></span>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Kategori</label>
                                <select name="kategori" class="form-control">
                                    <option value="">-- Pilh --</option>
                                    
                                </select>
                                <span class="invalid-feedback"></span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- End Bootstrap modal -->

    <script src="<?=base_url();?>assets/jquery-3.3.1/jquery-3.3.1.min.js"></script>
    <script src="<?=base_url();?>assets/popper/popper.min.js"></script>
    <script src="<?=base_url();?>assets/bootstrap-4/js/bootstrap.min.js"></script>
    <script src="<?=base_url();?>assets/DataTables-1.10.18/js/jquery.dataTables.min.js"></script>
    <script src="<?=base_url();?>assets/DataTables-1.10.18/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?=base_url();?>assets/sweetalert2/sweetalert2.all.min.js"></script>

    <script type="text/javascript">
    var save_label;
    var table;

    $(document).ready(function() {
        
        $.fn.dataTableExt.oApi.fnPagingInfo = function(oSettings)
    	{
			return {
				"iStart": oSettings._iDisplayStart,
				"iEnd": oSettings.fnDisplayEnd(),
				"iLength": oSettings._iDisplayLength,
				"iTotal": oSettings.fnRecordsTotal(),
				"iFilteredTotal": oSettings.fnRecordsDisplay(),
				"iPage": Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength),
				"iTotalPages": Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength)
			};
		};
        
        table = $("#barang").DataTable({
			initComplete: function() {
				var api = this.api();
				$('#barang_filter input')
					.off('.DT')
					.on('keyup.DT', function(e) {
                        api.search(this.value).draw();
                    });
			},
			oLanguage: {
                sProcessing: "loading..."
			},
			processing: true,
			serverSide: true,
			ajax: {
                "url": "<?= base_url().'barang/get_guest_json'?>",
                "type": "POST"
            },
			columns: [
                {
                    "data": "id_barang",
                    "orderable": false,
                    "searchable": false
                },
				{"data": "nama_barang"},
				{"data": "stok"},
				{"data": "kategori_barang"},
				{
                    "data": "view",
                    "orderable": false,
                    "searchable": false
                }
			],
			order: [[1, 'asc']],
			rowId: function(a){
                return a;
            },
            rowCallback: function(row, data, iDisplayIndex) {
				var info = this.fnPagingInfo();
				var page = info.iPage;
				var length = info.iLength;
                var index = page * length + (iDisplayIndex + 1);
				$('td:eq(0)', row).html(index);
			}
		});

        $('#modal_form').on('shown.bs.modal', function (e) {
            load_kategori();
        });

        $('#modal_form').on('hidden.bs.modal', function (e) {
            var inputs = $('#form input, #form textarea, #form select');
            inputs.removeClass('is-valid is-invalid');
        });
    });

    function load_kategori(){
        $.ajax({
            url: "<?=base_url('barang/getKategori')?>",
            method: 'GET',
            dataType: 'JSON',
            success: function(categories){
                console.log(categories);
                var opsi_kategori;
                $('[name="kategori"]').html('');
                $.each(categories, function(key, val){
                    opsi_kategori = `<option value="${val.id_kategori}">${val.kategori_barang}</option>`;
                    $('[name="kategori"]').append(opsi_kategori);
                });
            }
        });
    }

    function reload_ajax(){
        table.ajax.reload(null, false);
    }

    function swalert(method){
        Swal({
            title: 'Success',
            text: 'Data berhasil '+method,
            type: 'success'
        });
    };

    function add_barang()
    {
        save_label = 'add';
        $('#form')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        $('#modal_form').modal('show'); // show bootstrap modal
        $('.modal-title').text('Tambah Barang'); // Set Title to Bootstrap modal title
    }

    function edit_barang(id)
    {
        save_label = 'update';
        $('#form')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string

        //Ajax Load data from ajax
        $.ajax({
            url : "<?=base_url('barang/edit/')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                $('[name="id"]').val(data.id_barang);
                $('[name="nama_barang"]').val(data.nama_barang);
                $('[name="stok"]').val(data.stok);
                $('[name="kategori"]').val(data.kategori_id);
                $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Edit Barang'); // Set title to Bootstrap modal title
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }

    function save()
    {
        $('#btnSave').text('saving...'); //change button text
        $('#btnSave').attr('disabled',true); //set button disable 
        var url, method;

        if(save_label == 'add') {
            url = "<?=base_url('barang/add')?>";
            method = 'disimpan';
        } else {
            url = "<?=base_url('barang/update')?>";
            method = 'diupdate';
        }

        // ajax adding data to database
        $.ajax({
            url : url,
            type: "POST",
            data: $('#form').serialize(),
            dataType: "json",
            success: function(data)
            {
                console.log(data);
                if(data.status) //if success close modal and reload ajax table
                {
                    $('#modal_form').modal('hide');
                    reload_ajax();
                    swalert(method);
                }
                else
                {
                    $.each(data.errors, function(key, value){
                        $('[name="'+key+'"]').addClass('is-invalid'); //select parent twice to select div form-group class and add has-error class
                        $('[name="'+key+'"]').next().text(value); //select span help-block class set text error string
                        if(value == ""){
                            $('[name="'+key+'"]').removeClass('is-invalid');
                            $('[name="'+key+'"]').addClass('is-valid');
                        }
                    });
                }
                $('#btnSave').text('save'); //change button text
                $('#btnSave').attr('disabled',false); //set button enable 
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error adding / update data');
                $('#btnSave').text('save'); //change button text
                $('#btnSave').attr('disabled',false); //set button enable 
            }
        });

        $('#form input').on('keyup', function(){
            $(this).removeClass('is-valid is-invalid');            
        });
        $('#form select').on('change', function(){
            $(this).removeClass('is-valid is-invalid');
        });
    }

    function hapus_barang(id)
    {
        Swal({
            title: 'Anda yakin?',
            text: "Data barang akan dihapus!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Hapus data!'
        }).then((result) => {
            if(result.value) {
                $.ajax({
                    url : "<?=base_url('barang/delete')?>/"+id,
                    type: "POST",
                    success: function(data)
                    {
                        reload_ajax();
                        swalert('dihapus');
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        alert('Error deleting data');
                    }
                });
            }
        });
    }
    </script>
</body>
</html>