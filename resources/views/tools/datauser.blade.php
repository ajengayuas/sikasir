@extends('admin.admin')

@section('title')
Manage User
@endsection

@section('content_header')
Data User
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        @can('user-create')
        <button type="button" class="btn btn-primary" id="btntambah">
          Tambah
        </button>
        @endcan
        <br><br>
        <table class="table table-hover table-bordered table-stripped" id="datatables">
          <thead>
            <tr>
              <th>No.</th>
              <th>Nama</th>
              <th>Email</th>
              <th>Role</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="adduser" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Tambah User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="" id="userform" name="userform" class="form-horizontal" method="post">
          @csrf
          <input type="hidden" id="id">
          <table width="500" align="center">
            <tr>
              <td style="padding-left:20px">
                <label>Nama</label>
              </td>
              <td style="padding-left:20px">
                <input type="text" class="form-control" id="name" placeholder="Nama" name="name" style="width:250px;">
              </td>
            </tr>
            <tr>
              <td style="padding-left:20px">
                <label>Email</label>
              </td>
              <td style="padding-left:20px">
                <input type="text" class="form-control" id="email" placeholder="Email" name="email" style="width:250px;">
              </td>
            </tr>
            <tr>
              <td style="padding-left:20px">
                <label>Password</label>
              </td>
              <td style="padding-left:20px">
                {!! Form::password('password', array('placeholder' => 'Password','class' => 'form-control', 'id'=>'password')) !!}
              </td>
            </tr>
            <tr>
              <td style="padding-left:20px">
                <label>Confirm Password</label>
              </td>
              <td style="padding-left:20px">
                {!! Form::password('confirm-password', array('placeholder' => 'Confirm Password','class' => 'form-control', 'id'=>'confirm-password')) !!}
              </td>
            </tr>
            <tr>
              <td style="padding-left:20px">
                <label>Role</label>
              </td>
              <td style="padding-left:20px">
                {!! Form::select('roles', $roles,$userRole, array('class' => 'form-control', 'id'=>'nmrole')) !!}
              </td>
            </tr>
          </table>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="btnsimpan">Simpan</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Keluar</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<script type="text/javascript">
  $(document).ready(function() {
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    var username = @JSON($userlogin);

    $('#datatables').DataTable({
      processing: true,
      serverSide: true,
      ajax: "{{ route('datauser') }}",
      columns: [{
          data: 'DT_RowIndex',
          name: 'DT_RowIndex'
        },
        {
          data: 'name',
          name: 'name'
        },
        {
          data: 'email',
          name: 'email'
        },
        {
          data: 'nmrole',
          name: 'nmrole'
        },
        {
          data: 'action',
          name: 'action',
          orderable: false,
          searchable: false
        },
      ]
    });

    $('#btntambah').click(function(e) {
      $('#name').val('');
      $('#email').val('');
      $('#password').val('');
      $('#confirm-password').val('');
      $('#exampleModalLabel').text('Tambah User');
      $('#adduser').modal({
        show: true,
        backdrop: 'static'
      });
    });

    $('body').on('click', '#btnedit', function() {
      let id = $(this).attr('data-id');
      console.log(id)
      $.ajax({
        type: "post",
        url: "{!! route('edituser') !!}",
        data: {
          "_token": "{{ csrf_token() }}",
          id: id
        },
        dataType: "json",
        success: function(response) {
          let data = response.data;
          $('#id').val(data.id);
          $('#name').val(data.name);
          $('#email').val(data.email);
          $('#password').val(data.password);
          $('#confirm-password').val(data.password);
          $('#nmrole').val(data.nmrole);
          $('#exampleModalLabel').text('Update Menu');
          $('#adduser').modal({
            show: true,
            backdrop: 'static'
          });
        }
      });
    });

    $('#btnsimpan').click(function(e) {
      $('#btnsimpan').html('<i class="fas fa-hourglass"></i> Please Wait')
      $('#btnsimpan').prop('disabled', true);
      let id = $('#id').val();
      let name = $('#name').val();
      let email = $('#email').val();
      let password = $('#password').val();
      let confirm = $('#confirm-password').val();
      let nmrole = $('#nmrole').val()
      let aktif = 1
      if (name == '' || name == null) {
        $('#btnsimpan').html('Simpan')
        $('#btnsimpan').prop('disabled', false);
        notifalert('Nama');
      } else if (email == '' || email == null) {
        $('#btnsimpan').html('Simpan')
        $('#btnsimpan').prop('disabled', false);
        notifalert('Email');
      } else if (password == '' || password == null) {
        $('#btnsimpan').html('Simpan')
        $('#btnsimpan').prop('disabled', false);
        notifalert('Password');
      } else if (confirm == '' || confirm == null) {
        $('#btnsimpan').html('Simpan')
        $('#btnsimpan').prop('disabled', false);
        notifalert('Confirm Password');
      } else if (password != confirm) {
        $('#btnsimpan').html('Simpan')
        $('#btnsimpan').prop('disabled', false);
        notifalert2('Password');
      } else {
        $.ajax({
          type: "post",
          url: (id == null || id == '') ? "{!! route('storeuser') !!}" : "{!! route('updateuser') !!}",
          data: {
            "_token": "{{ csrf_token() }}",
            'id': id,
            'name': name,
            'email': email,
            'password': password,
            'confirm-password': confirm,
            'roles': nmrole,
            'aktif': aktif,
            'created_by': username,
            'updated_by': username,
          },
          dataType: "json",
          success: function(response) {
            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: (response.status == 'error') ? 'error' : 'success',
              title: response.message,
              showConfirmButton: false,
              timer: 1500
            }).then((result) => {
              if (response.status == 'success') {
                $('#userform').trigger("reset");
                $('#adduser').modal('hide');
                $('#btnsimpan').html('Simpan');
                $('#btnsimpan').prop('disabled', false);
                $("#datatables").DataTable().ajax.reload(null, false);
              } else {
                $('#btnsimpan').html('Simpan');
                $('#btnsimpan').prop('disabled', false);
              }
            });
          },
          error: function(xhr, status, error) {
            $('#btnsimpan').html('Simpan');
            $('#btnsimpan').prop('disabled', false);
            Swal.fire({
              title: 'Data Gagal Disimpan!',
              text: 'Cek Data',
              icon: 'error'
            });
            return;
          }
        });
      }
    });

    $('body').on('click', '#btndelete', function(e) {
      let id = $(this).attr('data-id');
      Swal.fire({
        title: 'Perhatian',
        text: "Apakah Anda Yakin Menghapus Data Ini ?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            type: "post",
            url: "{!! url('hapususer') !!}",
            data: {
              "_token": "{{ csrf_token() }}",
              id: id
            },
            dataType: "json",
            success: function(response) {
              Swal.fire({
                title: response.title,
                icon: (response.status == 'error') ? 'error' : 'success',
                text: response.message,
              }).then((result) => {
                if (response.status == 'success') {
                  $("#datatables").DataTable().ajax.reload(null, false);
                }
              });
            },
            error: function(xhr, status, error) {
              Swal.fire({
                title: 'Data Gagal Disimpan!',
                text: 'Cek Data',
                icon: 'error'
              });
              return;
            }
          })
        }
      });
    });

    function notifalert(params) {
      Swal.fire({
        title: 'Informasi',
        text: params + ' Tidak Boleh Kosong',
        icon: 'warning'
      });
      return;
    }

    function notifalert2(params) {
      Swal.fire({
        title: 'Informasi',
        text: params + ' Tidak Sama',
        icon: 'warning'
      });
      return;
    }
  });
</script>
@endsection