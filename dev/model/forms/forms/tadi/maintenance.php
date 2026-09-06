<?php $msg = $config['message'] ?? 'We will be back shortly.'; ?>
<style>
    .tadi-maintenance {
        min-height: 55vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        /* background: #0f172a; */
        color: #001736;
        text-align: center;
    }
    .tadi-maintenance__box {
        max-width: 420px;
    }
    .tadi-maintenance h1 {
        font-size: 1.5rem;
        margin-bottom: .5rem;
    }
    .tadi-maintenance p {
        color: #454e59;
    }
</style>
<section class="tadi-maintenance" role="status">
    <div class="tadi-maintenance__box">
        <h1>Under Maintenance</h1>
        <p><?php echo htmlspecialchars($msg); ?></p>
    </div>
</section>