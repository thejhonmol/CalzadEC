/**
 * Datos de Provincias y Ciudades de Ecuador
 */
const ecuadorDatos = {
    "Azuay": ["Cuenca", "Gualaceo", "Paute", "Sigsig", "Chordeleg"],
    "Bolívar": ["Guaranda", "Chillanes", "Chimbo", "Echeandía", "San Miguel"],
    "Cañar": ["Azogues", "Biblián", "Cañar", "La Troncal"],
    "Carchi": ["Tulcán", "Bolívar", "Espejo", "Mira", "Montúfar"],
    "Chimborazo": ["Riobamba", "Alausí", "Chambo", "Chunchi", "Guano", "Pallatanga", "Penipe"],
    "Cotopaxi": ["Latacunga", "La Maná", "Pangua", "Pujilí", "Salcedo", "Saquisilí"],
    "El Oro": ["Machala", "Arenillas", "Atahualpa", "Balsas", "Chilla", "Huaquillas", "Pasaje", "Piñas", "Santa Rosa"],
    "Esmeraldas": ["Esmeraldas", "Atacames", "Eloy Alfaro", "Muisne", "Quinindé", "San Lorenzo"],
    "Galápagos": ["Puerto Baquerizo Moreno", "Puerto Ayora", "Puerto Villamil"],
    "Guayas": ["Guayaquil", "Durán", "Milagro", "Samborondón", "Daule", "Playas", "Naranjal", "Salitre"],
    "Imbabura": ["Ibarra", "Atuntaqui", "Cotacachi", "Otavalo", "Pimampiro"],
    "Loja": ["Loja", "Catamayo", "Cariamanga", "Macará", "Zapotillo"],
    "Los Ríos": ["Babahoyo", "Quevedo", "Buena Fe", "Mocache", "Puebloviejo", "Valencia", "Ventanas", "Vinces"],
    "Manabí": ["Portoviejo", "Manta", "Chone", "El Carmen", "Bahía de Caráquez", "Jipijapa", "Montecristi", "Pedernales"],
    "Morona Santiago": ["Macas", "Gualaquiza", "Limón Indanza", "Sucúa"],
    "Napo": ["Tena", "Archidona", "El Chaco", "Quijos"],
    "Orellana": ["Puerto Francisco de Orellana", "La Joya de los Sachas", "Loreto"],
    "Pastaza": ["Puyo", "Mera", "Santa Clara"],
    "Pichincha": ["Quito", "Cayambe", "Machachi", "Sangolquí", "Tabacundo"],
    "Santa Elena": ["Santa Elena", "La Libertad", "Salinas"],
    "Santo Domingo de los Tsáchilas": ["Santo Domingo"],
    "Sucumbíos": ["Nueva Loja", "Lumbaqui", "Puerto El Carmen"],
    "Tungurahua": ["Ambato", "Baños", "Cevallos", "Mocha", "Pelileo", "Píllaro", "Quero"],
    "Zamora Chinchipe": ["Zamora", "Chinchipe", "El Pangui", "Yantzaza"]
};

function cargarProvincias(selectElementId, selectCiudadId, selectedProvincia = '', selectedCiudad = '') {
    const selectProvincia = document.getElementById(selectElementId);
    const selectCiudad = document.getElementById(selectCiudadId);

    // Limpiar y cargar provincias
    selectProvincia.innerHTML = '<option value="">Seleccione una provincia...</option>';
    Object.keys(ecuadorDatos).sort().forEach(provincia => {
        const option = document.createElement('option');
        option.value = provincia;
        option.textContent = provincia;
        if (provincia === selectedProvincia) option.selected = true;
        selectProvincia.appendChild(option);
    });

    // Evento de cambio
    selectProvincia.onchange = function () {
        const provincia = this.value;
        actualizarCiudades(selectCiudadId, provincia);
    };

    // Cargar ciudades iniciales si hay provincia seleccionada
    if (selectedProvincia) {
        actualizarCiudades(selectCiudadId, selectedProvincia, selectedCiudad);
    }
}

function actualizarCiudades(selectCiudadId, provincia, selectedCiudad = '') {
    const selectCiudad = document.getElementById(selectCiudadId);
    selectCiudad.innerHTML = '<option value="">Seleccione una ciudad...</option>';

    if (provincia && ecuadorDatos[provincia]) {
        ecuadorDatos[provincia].sort().forEach(ciudad => {
            const option = document.createElement('option');
            option.value = ciudad;
            option.textContent = ciudad;
            if (ciudad === selectedCiudad) option.selected = true;
            selectCiudad.appendChild(option);
        });
    }
}
