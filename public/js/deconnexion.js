function Deconnexion() {
    window.location.href = "/logout";
}
const delai = 300000;
let compteARebours = setTimeout(Deconnexion, delai);
document.addEventListener("mousemove", function() {
    clearTimeout(compteARebours);
    compteARebours = setTimeout(Deconnexion, delai);
});
document.addEventListener("keypress", function() {
    clearTimeout(compteARebours);
    compteARebours = setTimeout(Deconnexion, delai);
});