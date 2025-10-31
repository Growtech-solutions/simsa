import face_recognition
import numpy as np
import os
import sys 

# Ruta de la carpeta donde están las fotos de los trabajadores
WORKERS_FOLDER = "/var/www/transimex/documentos/RecursosHumanos/fotos_trabajadores"

# Cargar rostros de trabajadores
known_faces = []  # Lista para almacenar las codificaciones de los rostros conocidos
known_ids = []    # Lista para almacenar los IDs de los trabajadores

# Cargar las imágenes de los trabajadores y obtener las codificaciones de los rostros
for filename in os.listdir(WORKERS_FOLDER):
    if filename.endswith(".jpg") or filename.endswith(".png"):
        worker_id = filename.split(".")[0]  # ID del trabajador (nombre del archivo sin extensión)
        image_path = os.path.join(WORKERS_FOLDER, filename)
        
        # Cargar la imagen y obtener las codificaciones de los rostros
        image = face_recognition.load_image_file(image_path)
        face_encodings = face_recognition.face_encodings(image)

        if face_encodings:
            known_faces.append(face_encodings[0])  # Solo tomamos el primer rostro de la imagen
            known_ids.append(worker_id)

# Obtener la ruta de la imagen enviada como argumento
image_path = sys.argv[1]

# Cargar la imagen capturada
unknown_image = face_recognition.load_image_file(image_path)
unknown_encodings = face_recognition.face_encodings(unknown_image)

if unknown_encodings:
    unknown_encoding = unknown_encodings[0]  # Tomar el primer rostro detectado en la imagen desconocida

    # Comparar la imagen desconocida con los rostros conocidos
    matches = face_recognition.compare_faces(known_faces, unknown_encoding, tolerance=0.5)
    face_distances = face_recognition.face_distance(known_faces, unknown_encoding)

    if True in matches:
        best_match_index = np.argmin(face_distances)  # Obtener el índice del mejor match
        worker_id = known_ids[best_match_index]  # Obtener el ID del trabajador con la mejor coincidencia
        print(worker_id)  # Imprimir el ID del trabajador
    else:
        print("Desconocido")  # Si no hay coincidencias, se devuelve "Desconocido"
else:
    print("Desconocido")  # Si no se detecta rostro en la imagen desconocida