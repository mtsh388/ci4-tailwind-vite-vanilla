<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-name" content="<?= csrf_token() ?>">
  <meta name="csrf-hash" content="<?= csrf_hash() ?>">
  <title><?= esc($title ?? 'Starter Kit') ?></title>

  <!-- PREVENT FOUC -->
  <style>
    body.loading {
      overflow: hidden;
    }

    body.loading #app {
      visibility: hidden;
    }

    #pageLoader {
      position: fixed;
      inset: 0;
      z-index: 99999;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(6px);
    }

    .loader-hidden {
      display: none !important;
    }


    .loader-spinner {
      width: 56px;
      height: 56px;
      border-radius: 9999px;
      border: 4px solid #bfdbfe;
      border-top-color: #2563eb;
      animation: spin .8s linear infinite;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }
  </style>



  <!-- VITE DEV -->
  <?php if (ENVIRONMENT === 'development'): ?>

    <script type="module" src="http://localhost:5173/@vite/client"></script>

    <script type="module" src="http://localhost:5173/resources/js/app.js"></script>

  <?php else: ?>

    <!-- BUILD CSS -->
    <link rel="stylesheet" href="<?= base_url('build/assets/app.css') ?>">

    <!-- BUILD JS -->
    <script type="module" src="<?= base_url('build/assets/app.js') ?>"></script>

  <?php endif; ?>
  <script>
    (() => {

      const theme = localStorage.getItem('theme')

      if (
        theme === 'dark' ||
        (
          !theme &&
          window.matchMedia('(prefers-color-scheme: dark)').matches
        )
      ) {

        document.documentElement.classList.add('dark')

      }

    })()
  </script>
</head>

<body class="loading bg-slate-100 text-slate-800 dark:bg-slate-950 dark:text-white transition-colors duration-300" data-success="<?= session()->getFlashdata('success') ?>"
  data-error="<?= session()->getFlashdata('error') ?>">
  <!-- PAGE LOADER -->
  <div id="pageLoader">

    <div style="display:flex;flex-direction:column;align-items:center">

      <div class="loader-spinner"></div>

      <p style="margin-top:16px;font-size:14px;font-weight:500;color:#475569">
        Loading...
      </p>

    </div>

  </div>
  <div id="app" class="flex min-h-screen overflow-x-hidden">
    <div
      id="sidebarOverlay"
      class="fixed inset-0 z-40 hidden bg-black/50 backdrop-blur-sm md:hidden">
    </div>
    <!-- SIDEBAR -->
    <?= $this->include('layouts/sidebar') ?>

    <!-- CONTENT -->
    <div class="flex min-w-0 flex-1 flex-col md:ml-0">

      <!-- NAVBAR -->
      <?= $this->include('layouts/navbar') ?>

      <!-- MAIN CONTENT -->
      <main class="ex-1 p-4 sm:p-6">

        <?= $this->renderSection('content') ?>

      </main>

      <!-- FOOTER -->
      <?= $this->include('layouts/footer') ?>

    </div>

  </div>

</body>

</html>