import cv2
import mysql.connector
from pyzbar.pyzbar import decode
from flask import Flask, Response, render_template_string, redirect, url_for, jsonify, session, flash
from datetime import datetime, date, time, timedelta

app = Flask(__name__)
app.secret_key = 'your_secret_key'  # secret key untuk sesi manager

# Koneksi ke database
try:
    db = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="employee_db"
    )
    print("Koneksi ke database berhasil!")
except mysql.connector.Error as err:
    print(f"Error: {err}")
    exit()

# Variasi global untuk menyimpan data qr code
qr_data = None

def generate_frames():
    """Fungsi untuk menghasilkan frame video dari kamera."""
    global qr_data
    cap = cv2.VideoCapture(0)  # id kamera pertama
    while True:
        success, frame = cap.read()
        if not success:
            break
        else:
            # membaca sandi qr code
            for qr in decode(frame):
                qr_data = qr.data.decode('utf-8')  # Ambil data qr
                print(f"QR Code Terdeteksi: {qr_data}")
                cap.release()
                cv2.destroyAllWindows()
                return

            ret, buffer = cv2.imencode('.jpg', frame)
            frame = buffer.tobytes()
            yield (b'--frame\r\n'
                   b'Content-Type: image/jpeg\r\n\r\n' + frame + b'\r\n')

def is_shift_active(shift, current_time):
    start_time = shift['start_time']
    end_time = shift['end_time']
    
    if start_time < end_time:
        # Shift tidak melewati tengah malam
        return start_time <= current_time <= end_time
    else:
        # Shift melewati tengah malam
        return current_time >= start_time or current_time <= end_time

def check_qr_code_in_db(qr_data):
    """Cek apakah data QR code ada di database dan simpan waktu scan."""
    cursor = db.cursor(dictionary=True)

    # Pisahkan data dari QR code
    try:
        lines = qr_data.split("\n")
        id_karyawan = lines[0].split(": ")[1]  # Ambil ID karyawan
        nama = lines[1].split(": ")[1]  # Ambil nama
    except Exception as e:
        print(f"Error parsing QR data: {e}")
        return False, None

    # Query untuk mencari data di database
    query = "SELECT * FROM employees WHERE id_karyawan = %s AND nama = %s"
    cursor.execute(query, (id_karyawan, nama))
    result = cursor.fetchone()

    if result:
        print(f"QR Code cocok: {result}")
        # set variabel sesi
        session['user_id'] = result['id_karyawan']  # Menggunakan id_karyawan sebagai pengganti id
        session['user'] = nama

        # Tentukan pergeseran saat ini berdasarkan waktu saat ini
        current_time = datetime.now().time()
        print(f"Current time: {current_time}")

        # Query untuk mencari shift yang sesuai dengan waktu saat ini
        cursor.execute("SELECT * FROM shift")
        shifts = cursor.fetchall()
        
        active_shift = None
        for shift in shifts:
            start_time = datetime.strptime(str(shift['jam_masuk']), '%H:%M:%S').time()
            end_time = datetime.strptime(str(shift['jam_pulang']), '%H:%M:%S').time()
            shift['start_time'] = start_time
            shift['end_time'] = end_time
            if is_shift_active(shift, current_time):
                active_shift = shift
                break

        if not active_shift:
            print("No shifts available for the current time.")
            return False, 'no_shift_available'
        
        shift_id = active_shift['id_shift']  # Menggunakan id_shift sebagai pengganti id
        print(f"Shift found: {active_shift}")

        # Cek apakah sudah ada scan hari ini
        today = date.today()
        scan_query = "SELECT * FROM attendance WHERE karyawan_id = %s AND tanggal = %s"
        cursor.execute(scan_query, (result['id_karyawan'], today))
        scan_result = cursor.fetchall()

        if len(scan_result) == 0:
            # Belum ada scan hari ini, simpan sebagai "masuk"
            insert_query = "INSERT INTO attendance (karyawan_id, shift_id, tanggal, jam_masuk) VALUES (%s, %s, %s, %s)"
            cursor.execute(insert_query, (result['id_karyawan'], shift_id, today, datetime.now().time()))
        elif len(scan_result) == 1:
            # Sudah ada satu scan hari ini, simpan sebagai "pulang"
            update_query = "UPDATE attendance SET jam_pulang = %s WHERE karyawan_id = %s AND tanggal = %s"
            cursor.execute(update_query, (datetime.now().time(), result['id_karyawan'], today))
        else:
            print("Karyawan sudah melakukan scan masuk dan pulang hari ini.")
            return False, 'already_absent'

        db.commit()
        return True, None  # Data cocok
    else:
        print("QR Code tidak ditemukan di database.")
        return False, 'qr_not_found'  # Data tidak cocok

@app.route('/')
def home():
    """Halaman utama."""
    message = session.pop('message', None)
    return render_template_string('''
    <h1>Scan QR Code</h1>
    <p>Klik tombol di bawah untuk memulai scan QR Code.</p>
    <a href="/scan">
        <button>Mulai Scan</button>
    </a>
    {% if message %}
    <script>
        alert('{{ message }}');
        window.location.href = "http://localhost/dashboard-absensi/template/login.php";
    </script>
    {% endif %}
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            flex-direction: column;
            overflow-x: hidden;
            min-height: 100vh;
            letter-spacing: 1px;
            background: linear-gradient(to left, #F1FAFF, #CBE4FF, #F1FAFF);
        }
        h1 {
            font-size: 36px;
            color: #333;
            margin-bottom: 20px;
        }
        p {
            font-size: 18px;
            color: #555;
            margin-bottom: 40px;
        }
        .button-container {
            margin-top: 20px;
        }
        button {
            border: none;
            padding: 12px 20px;
            font-size: 18px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
            background-color: #17a2b8;
            color: white;
            border: none;
            border-radius: 20px;
            transition: all 2s ease;
            box-shadow: inset 15px 15px 15px rgba(255, 255, 255, 0.7), 3px 3px 3px rgba(0, 0, 0, 0.3), 3px 0 3px 0 rgba(255, 255, 255, 0.7);
        }
        button:hover {
            background: rgb(130, 130, 130);
            text-decoration: none;
            color: black;
        }
    </style>
    ''', message=message)

@app.route('/scan', methods=['GET'])
def scan():
    """Halaman scan QR code."""
    global qr_data
    qr_data = None
    return render_template_string('''
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            flex-direction: column;
            overflow-x: hidden;
            background: linear-gradient(to left, #F1FAFF, #CBE4FF, #F1FAFF);
            color: #333; /* Warna teks yang lebih lembut */
        }

        .container {
            text-align: center;
            padding: 20px;
            border-radius: 15px;
            background: #ffffff; /* Warna latar belakang putih */
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1); /* Bayangan lembut */
            max-width: 400px;
            width: 90%;
        }

        h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #007BFF; /* Biru yang lembut */
        }

        p {
            font-size: 16px;
            margin-bottom: 20px;
            line-height: 1.6;
            color: #555;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007BFF;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2); /* Bayangan tombol */
        }

        button:hover {
            background-color:

 #0056b3; /* Biru lebih gelap saat hover */
        }

        img {
            margin-top: 20px;
            max-width: 100%;
            height: auto;
            border: 2px solid #007BFF; /* Warna border gambar */
            border-radius: 10px; /* Membuat border gambar melengkung */
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2); /* Bayangan gambar */
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 20px;
            }

            p {
                font-size: 14px;
            }

            button {
                font-size: 14px;
                padding: 8px 16px;
            }
        }
    </style>
    <div class="container">
        <h1>Scan QR Code</h1>
        <p>Tunggu hingga QR Code terdeteksi.</p>
        <img src="{{ url_for('video_feed') }}" width="640" height="480">
    </div>
    <script>
        setInterval(function() {
            fetch('/check_qr_code').then(response => response.json()).then(data => {
                if (data.qr_data) {
                    window.location.href = data.redirect_url;
                }
            });
        }, 1000);
    </script>
    ''')

@app.route('/video_feed')
def video_feed():
    """Route untuk streaming video dari kamera."""
    return Response(generate_frames(), mimetype='multipart/x-mixed-replace; boundary=frame')

@app.route('/check_qr_code')
def check_qr_code():
    """Route untuk memeriksa QR code yang terdeteksi."""
    global qr_data
    if qr_data:
        print(f"QR Data: {qr_data}")  # Debugging statement
        success, redirect_route = check_qr_code_in_db(qr_data)
        print(f"Success: {success}, Redirect Route: {redirect_route}")  # Debugging statement
        if success:
            return {'qr_data': qr_data, 'redirect_url': 'http://localhost/dashboard-absensi/template/login.php?success=true'}
        elif redirect_route == 'already_absent':
            session['message'] = 'Anda telah absen masuk dan pulang!'
            return {'qr_data': qr_data, 'redirect_url': url_for('home')}
        elif redirect_route == 'qr_not_found':
            return {'qr_data': qr_data, 'redirect_url': url_for('qr_not_found')}
        elif redirect_route == 'no_shift_available':
            session['message'] = 'Tidak ada shift yang tersedia.'
            return {'qr_data': qr_data, 'redirect_url': url_for('home')}
    return {'qr_data': None}

@app.route('/login')
def login():
    """Halaman login setelah QR code cocok."""
    return redirect("http://localhost/dashboard-absensi/template/login.php", code=302)

@app.route('/qr_not_found')
def qr_not_found():
    """Halaman jika QR code tidak ditemukan di database."""
    return '''
    <h1>QR Code Tidak Ditemukan</h1>
    <p>QR Code yang discan tidak cocok dengan data di database.</p>
    <a href="/">Coba Lagi</a>
    '''

if __name__ == '__main__':
    app.run(debug=True)
