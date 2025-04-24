<div id="toggle">
    <i class="indicator"></i>
</div>
<script>
    const body = document.querySelector("body");
    const toggle = document.getElementById("toggle");

    if (localStorage.getItem("theme") === "dark") {
        toggle.classList.add("active");
        body.classList.add("active");
    }

    toggle.onclick = function () {
        toggle.classList.toggle("active");
        body.classList.toggle("active");

        if (body.classList.contains("active")) {
            localStorage.setItem("theme", "dark");
        } else {
            localStorage.setItem("theme", "light");
        }
    };
</script>
