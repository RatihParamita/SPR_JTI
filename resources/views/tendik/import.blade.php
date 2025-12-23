<form action="{{ url('/tendik/import_ajax') }}" method="POST" id="form-import" enctype="multipart/form-data" onsubmit="importTendik(event)">
    @csrf
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" style="font-weight: bold;">Impor Data Tendik</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div>
                Pastikan format file Anda sesuai dengan template yang disediakan.
                <br>
                <a href="{{ asset('templates/template_tendik.xlsx') }}" class="btn btn-sm btn-import" download>
                    <i class="fas fa-download"></i> Unduh template Excel
                </a>
                <br>
                <br>
            </div>
            <div class="form-group">
                <label>Pilih File Excel:</label>
                <input type="file" class="form-control" style="padding: 0px 0px 0px 0px; height: 32px;" id="file_tendik" name="file_tendik" required>
                <small id="error_file_tendik" class="form-text text-danger"></small>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                <i class="fas fa-times"></i> Batal
            </button>
            <button type="submit" class="btn btn-import">
                <i class="fas fa-upload"></i> Impor
            </button>
        </div>
    </div>
</form>

