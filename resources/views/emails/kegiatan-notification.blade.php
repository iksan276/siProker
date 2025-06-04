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
            background-color: #e7f3ff;
            border-left: 4px solid #4e73df;
            padding: 15px;
            margin: 20px 0;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $title }}</h1>
        </div>
        <div class="content">
            <p>Halo,</p>
            
            <div class="info-box">
                <p><strong>{{ $description }}</strong></p>
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
