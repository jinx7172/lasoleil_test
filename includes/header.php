<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}
// RESPONSABLE: PABLO (Lógica de sesión para contador del carrito)
$total_items = 0;
if(isset($_SESSION['carrito'])) { 
    foreach($_SESSION['carrito'] as $item) { 
        $total_items += $item['cantidad']; 
    } 
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>La Soleil - Panadería y Repostería</title>
    <!-- RESPONSABLE: PABLO (Estructura HTML) & LEO (Diseño en styles.css) -->
    <link rel="stylesheet" href="assets/css/styles.css">
    
    <style>
        /* RESPONSABLE: LEO (Diseño del reproductor) */
        .reproductor-container { position: fixed; bottom: 20px; right: 20px; z-index: 1000; display: flex; flex-direction: column; align-items: flex-end; }
        .btn-flotante { background: #ffb6c1; border: none; border-radius: 50%; width: 60px; height: 60px; font-size: 28px; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.2); transition: 0.3s; display: flex; justify-content: center; align-items: center; margin-top: 10px; }
        .btn-flotante:hover { background: #d87093; }
        .playlist-panel { background: white; border-radius: 15px; padding: 15px; width: 280px; box-shadow: 0 8px 25px rgba(0,0,0,0.2); border: 2px solid #ffb6c1; display: none; }
        .playlist-panel.mostrar { display: block; animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .cancion-item { display: flex; justify-content: space-between; align-items: center; padding: 8px; border-bottom: 1px solid #eee; font-family: 'Quicksand', sans-serif; font-size: 14px; color: #5d4037; cursor: pointer; border-radius: 5px; }
        .cancion-item:hover { background: #fff0f5; }
        .cancion-item.activa { background: #ffe4e1; font-weight: bold; color: #d87093; }
        .btn-loop { background: none; border: none; cursor: pointer; font-size: 16px; opacity: 0.3; transition: 0.2s; }
        .btn-loop.activo { opacity: 1; color: #ff69b4; transform: scale(1.1); }
    </style>
</head>
<body>

<!-- REPRODUCTOR DE MÚSICA -->
<!-- RESPONSABLE: CARLOS (Lógica del reproductor y playlist) -->
<audio id="musicaFondo"></audio>
<div class="reproductor-container">
    <div class="playlist-panel" id="playlistPanel">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #ffb6c1; padding-bottom: 10px;">
            <h4 style="margin: 0; color: #d87093;">🎶 Playlist La Soleil</h4>
            <button onclick="togglePanel()" style="background: none; border: none; font-size: 18px; cursor: pointer; color: #d87093;">✖</button>
        </div>
        <div style="text-align: center; margin-bottom: 15px;">
            <button id="btnPlayPause" onclick="toggleMusica()" class="btn" style="background: #ffb6c1; color: white; width: 100%; border: none; padding: 10px; border-radius: 5px; font-size: 16px; cursor: pointer;">▶ Reproducir</button>
        </div>
        <ul id="listaUI" style="list-style: none; padding: 0; margin: 0; max-height: 200px; overflow-y: auto;"></ul>
        <div style="margin-top: 15px; display: flex; align-items: center; gap: 10px; background: #fff0f5; padding: 8px; border-radius: 8px;">
            <span style="font-size: 16px;">🔈</span>
            <input type="range" id="volumenControl" min="0" max="1" step="0.05" value="0.5" style="flex-grow: 1; cursor: pointer; accent-color: #d87093;">
            <span style="font-size: 16px;">🔊</span>
        </div>
    </div>
    <button id="btnMusica" class="btn-flotante" onclick="togglePanel()">🎵</button>
</div>

<script>
    // RESPONSABLE: CARLOS (Lógica JavaScript de música)
    const listaCanciones = [
        { titulo: "Ciudad Fayenza", archivo: "assets/audio/ciudad_fayenza.mp3" },
        { titulo: "Pueblo Escaso", archivo: "assets/audio/littleroot_town.mp3" }
    ];
    let indiceActual = 0;
    let modoBucle = false; 
    const audio = document.getElementById('musicaFondo');
    const btnFlotante = document.getElementById('btnMusica');
    const btnPlayPause = document.getElementById('btnPlayPause');
    const listaUI = document.getElementById('listaUI');
    const panel = document.getElementById('playlistPanel');
    const volumenControl = document.getElementById('volumenControl');

    audio.volume = 0.5;
    volumenControl.addEventListener('input', function() { audio.volume = this.value; });

    function togglePanel() { panel.classList.toggle('mostrar'); }

    function dibujarLista() {
        listaUI.innerHTML = '';
        listaCanciones.forEach((cancion, index) => {
            const li = document.createElement('li');
            li.className = 'cancion-item' + (index === indiceActual ? ' activa' : '');
            const span = document.createElement('span');
            span.innerText = (index === indiceActual && !audio.paused) ? "▶ " + cancion.titulo : cancion.titulo;
            span.onclick = () => reproducirCancion(index); 
            const btnLoop = document.createElement('button');
            btnLoop.className = 'btn-loop' + (modoBucle && index === indiceActual ? ' activo' : '');
            btnLoop.innerText = '🔁';
            btnLoop.onclick = (e) => {
                e.stopPropagation(); 
                modoBucle = !modoBucle;
                audio.loop = modoBucle;
                dibujarLista(); 
            };
            li.appendChild(span); li.appendChild(btnLoop); listaUI.appendChild(li);
        });
        if(audio.paused) {
            btnPlayPause.innerHTML = '▶ Reproducir'; btnPlayPause.style.background = '#ffb6c1';
        } else {
            btnPlayPause.innerHTML = '⏸ Pausar'; btnPlayPause.style.background = '#d87093';
        }
    }

    function reproducirCancion(index) {
        indiceActual = index;
        audio.src = listaCanciones[indiceActual].archivo;
        audio.loop = modoBucle;
        audio.play();
        dibujarLista();
    }

    audio.addEventListener('ended', function() {
        if (!modoBucle) { indiceActual = (indiceActual + 1) % listaCanciones.length; reproducirCancion(indiceActual); }
    });

    function toggleMusica() {
        if (audio.paused) {
            if (!audio.src) { reproducirCancion(0); } else { audio.play(); dibujarLista(); }
        } else { audio.pause(); dibujarLista(); }
    }
    dibujarLista();
</script>

<!-- HEADER PRINCIPAL -->
<!-- RESPONSABLE: LEO (Diseño del logo y botones) & PABLO (Integración) -->
<header style="padding: 15px 20px; position: relative; display: flex; justify-content: center; align-items: center; flex-wrap: wrap; gap: 20px;">
    <a href="index.php" style="text-decoration: none; display: inline-block;">
        <img src="assets/img/logo.png" alt="La Soleil" style="height: 220px; width: auto; transition: transform 0.3s; margin: -30px 0;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
    </a>
    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap; justify-content: center;">
        <a href="index.php" class="btn-header" style="background: white; color: var(--rosa-oscuro); padding: 10px 20px; border-radius: 30px; font-weight: bold; text-decoration: none; box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: 0.3s;">🏠 Inicio</a>
        <a href="estadisticas.php" class="btn-header" style="background: white; color: var(--rosa-oscuro); padding: 10px 20px; border-radius: 30px; font-weight: bold; text-decoration: none; box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: 0.3s;">📊 Stats</a>
        <a href="inventario.php" class="btn-header" style="background: white; color: var(--rosa-oscuro); padding: 10px 20px; border-radius: 30px; font-weight: bold; text-decoration: none; box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: 0.3s;">🥣 Inventario</a>
        <a href="admin.php" class="btn-header" style="background: white; color: var(--rosa-oscuro); padding: 10px 20px; border-radius: 30px; font-weight: bold; text-decoration: none; box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: 0.3s;">➕ Nuevo</a>
        <a href="editar.php" class="btn-header" style="background: white; color: var(--rosa-oscuro); padding: 10px 20px; border-radius: 30px; font-weight: bold; text-decoration: none; box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: 0.3s;">🛠️ Panel</a>
        <a href="carrito.php" class="btn-header" style="background: var(--rosa-oscuro); color: white; padding: 10px 20px; border-radius: 30px; font-weight: bold; text-decoration: none; box-shadow: 0 2px 5px rgba(0,0,0,0.2); transition: 0.3s;">🛒 Carrito (<?php echo $total_items; ?>)</a>
    </div>
</header>

<div class="container">