document.addEventListener("change", async (e) => {
  const toggle = e.target.closest(".toggle-status");
  if (!toggle) return;

  const id = toggle.value;
  const url = toggle.dataset.url + "/" + id;

  toggle.disabled = true;

  try {
    const response = await fetch(url, {
      method: "POST",
      headers: {
        "X-Requested-With": "XMLHttpRequest",
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        id: id,
        checked: toggle.checked ? 1 : 0,
      }),
    });

    const result = await response.json();

    if (!result.status) {
      toggle.checked = !toggle.checked;

      swalToast({
        icon: "error",
        title: result.message || "Gagal update status",
      });
      return;
    }

    swalToast({
      icon: "success",
      title: result.message || "Status berhasil diupdate",
    });
  } catch (error) {
    toggle.checked = !toggle.checked;

    swalToast({
      icon: "error",
      title: "Terjadi kesalahan server",
    });
  } finally {
    toggle.disabled = false;
  }
});
