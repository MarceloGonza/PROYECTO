document.addEventListener("DOMContentLoaded", function() {
const figura = document.querySelectorAll(".figura");

figura.forEach((img) => {
    img.addEventListener("mousemove", (e) => {
        let lupa = document.querySelector("#lupa");

        if (!lupa) { 
            lupa = document.createElement("div");
            lupa.setAttribute("id", "lupa");
            document.body.appendChild(lupa);
        }

        const zoomLevel = 2; // Ajustar el nivel de zoom 
        const zoomSize = 200; // Ajustar el tamaño del zoom 
        const posX = e.pageX - img.offsetLeft;
        const posY = e.pageY - img.offsetTop;
        const bgPosX = -posX * zoomLevel + zoomSize / 2;
        let bgPosY;

        if (img.parentElement.parentElement.previousElementSibling) { 
            bgPosY = -(posY - img.height) * zoomLevel + zoomSize / 2;
        } else {
            bgPosY = -posY * zoomLevel + zoomSize / 2;
        }

        lupa.style.backgroundImage = `url(${img.src})`; //ajustes lupa
        lupa.style.backgroundSize = `${img.width * zoomLevel}px`;
        lupa.style.backgroundPosition = `${bgPosX}px ${bgPosY}px`;
        lupa.style.width = `${zoomSize}px`;
        lupa.style.height = `${zoomSize}px`;
        lupa.style.display = "block";
        lupa.style.position = "fixed";
        lupa.style.left = e.clientX + 20 + "px";
        lupa.style.top = e.clientY + 20 + "px";
    });

    img.addEventListener("mouseout", () => { //agrego evento al mouse
        const lupa = document.querySelector("#lupa");
        if (lupa) {
            lupa.style.display = "none";
        }
    });

    img.classList.add("imagen-lupa");
});
});

