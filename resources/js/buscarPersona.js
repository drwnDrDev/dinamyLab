import axios from "axios";

axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

axios.get("/api/personas")
    .then(response => {
        console.log(response.data);
    })
    .catch(error => {
        console.error("Error fetching personas:", error);
    });

console.log("Hola desde buscarPersona");
