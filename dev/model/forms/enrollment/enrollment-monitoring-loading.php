<style>
    body {
    margin: 0;
    font-family: Arial, sans-serif;
}

.loading {
    display: flex;
    justify-content: center;
    align-items: center;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
    z-index: 1000; /*  Ensure it is on top of other elements */
}

.loader {
    position: relative;
    width: 100px;
    height: 100px;
}

.dot {
    width: 20px;
    height: 20px;
    background-color: #bacbe6;
    border-radius: 50%;
    position: absolute;
    animation: dotFade 1.2s infinite ease-in-out both;
}

.dot:nth-child(1) { top: 0; left: 50%; margin-left: -10px; animation-delay: -1.1s; }
.dot:nth-child(2) { top: 14.64%; left: 85.36%; margin-left: -10px; animation-delay: -1s; }
.dot:nth-child(3) { top: 50%; left: 100%; margin-left: -10px; animation-delay: -0.9s; }
.dot:nth-child(4) { top: 85.36%; left: 85.36%; margin-left: -10px; animation-delay: -0.8s; }
.dot:nth-child(5) { top: 100%; left: 50%; margin-left: -10px; animation-delay: -0.7s; }
.dot:nth-child(6) { top: 85.36%; left: 14.64%; margin-left: -10px; animation-delay: -0.6s; }
.dot:nth-child(7) { top: 50%; left: 0; margin-left: -10px; animation-delay: -0.5s; }
.dot:nth-child(8) { top: 14.64%; left: 14.64%; margin-left: -10px; animation-delay: -0.4s; }

@keyframes dotFade {
    0%, 39%, 100% { opacity: 0; }
    40% { opacity: 1; }
}

</style>

<div class="loading" id="loading-display" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="loader">
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
    </div>
</div>
