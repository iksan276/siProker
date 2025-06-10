<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
        }
        .header {
            background-color: #4e73df;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }
        .header-logo {
            width: 40px;
            height: 40px;
            margin-right: 15px;
            flex-shrink: 0;
        }
        .header h1 {
            margin: 0;
            white-space: nowrap;
            width: 1px;
            font-size:24px;
            line-height: 40px; 
            display: flex;
            align-items: center;
        }
        .content {
            padding: 30px 20px;
            background-color: #f8f9fc;
            border-left: 1px solid #e3e6f0;
            border-right: 1px solid #e3e6f0;
        }
        .footer {
            padding: 20px;
            text-align: center;
            color: #666;
            background-color: #f8f9fc;
            border: 1px solid #e3e6f0;
            border-radius: 0 0 5px 5px;
        }
        .btn {
            background-color: #4e73df;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 15px;
        }
        .btn:hover {
            background-color: #2e59d9;
        }
           .info-box {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            border-left: 4px solid;
        }
           .info-box-warning { 
            background-color: #fef5e7; 
            border-left-color: #f6c23e; 
            color: #856404;
        }
        .info-box-success { 
            background-color: #d1ecf1; 
            border-left-color: #1cc88a; 
            color: #0c5460;
        }
        .info-box-danger { 
            background-color: #f8d7da; 
            border-left-color: #e74a3b; 
            color: #721c24;
        }
        .info-box-info { 
            background-color: #d1ecf1; 
            border-left-color: #36b9cc; 
            color: #0c5460;
        }
        .info-box-primary { 
            background-color: #e7f3ff; 
            border-left-color: #4e73df; 
            color: #004085;
        }
        .info-box-secondary { 
            background-color: #f8f9fa; 
            border-left-color: #858796; 
            color: #383d41;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .detail-table th,
        .detail-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e3e6f0;
        }
        .detail-table th {
            background-color: #f8f9fc;
            font-weight: bold;
            width: 30%;
        }
            .badge {
            display: inline-block;
            padding: 0.25em 0.6em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.375rem;
            color: #fff;
        }
        .badge-warning { background-color: #f6c23e; }
        .badge-success { background-color: #1cc88a; }
        .badge-danger { background-color: #e74a3b; }
        .badge-info { background-color: #36b9cc; }
        .badge-primary { background-color: #4e73df; }
        .badge-secondary { background-color: #858796; }
    </style>
</head>
<body>
    <div class="container">
              <div class="header">
            <img src="https://siproker.itp.ac.id/asset/itp.png" alt="Logo ITP" class="header-logo">
            <h1>{{ $title }}</h1>
        </div>
        <div class="content">
            <p>Halo <b>{{ $recipient->email ?? 'User' }}</b>,</p>
            
            <div class="info-box {{ isset($infoBoxType) ? 'info-box-' . $infoBoxType : 'info-box-primary' }}">
                <p style="font-size:16px; margin: 0;">{!! $description !!}</p>
            </div>
            
            <table class="detail-table">
                <tr>
                    <th>Nama Kegiatan:</th>
                    <td>{{ $kegiatan->Nama }}</td>
                </tr>
                <tr>
                    <th>Pengirim:</th>
                    <td>{{ $sender->name }} ({{ $sender->email }})</td>
                </tr>
                <tr>
                    <th>Tanggal Pengajuan:</th>
                    <td>{{ \Carbon\Carbon::now()->timezone('Asia/Jakarta')->format('d-m-Y H:i:s') }}</td>
                </tr>
                @if($kegiatan->TanggalMulai)
                <tr>
                    <th>Tanggal Mulai:</th>
                    <td>{{ \Carbon\Carbon::parse($kegiatan->TanggalMulai)->timezone('Asia/Jakarta')->format('d-m-Y') }}</td>
                </tr>
                @endif
                @if($kegiatan->TanggalSelesai)
                <tr>
                    <th>Tanggal Selesai:</th>
                    <td>{{ \Carbon\Carbon::parse($kegiatan->TanggalSelesai)->timezone('Asia/Jakarta')->format('d-m-Y') }}</td>
                </tr>
                @endif
                @if($kegiatan->RincianKegiatan)
                <tr>
                    <th>Rincian Kegiatan:</th>
                    <td>{{ Str::limit($kegiatan->RincianKegiatan, 200) }}</td>
                </tr>
                @endif
            </table>
            
            <p>Silakan login ke sistem untuk melihat detail lengkap dan memberikan persetujuan.</p>
            
            <p style="text-align: center; margin-top: 30px;">
                <a href="{{ url('/kegiatans?kegiatanID=' . $kegiatan->KegiatanID) }}" class="btn">
                    Lihat Detail Kegiatan
                </a>
            </p>
            
            <p style="margin-top: 30px; font-size: 12px; color: #666;">
                <strong>Catatan:</strong> Email ini dikirim secara otomatis oleh sistem SIPROKER.
                Mohon tidak membalas email ini.
            </p>
        </div>
        <div class="footer">
            <p>Terima kasih,<br><strong>Tim SIPROKER</strong></p>
            <p style="font-size: 12px; color: #999;">
                Institut Teknologi Padang<br>
                Sistem Informasi Program Kerja
            </p>
        </div>
    </div>
</body>
</html>
