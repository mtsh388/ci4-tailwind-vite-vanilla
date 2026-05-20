import DataTable from "datatables.net-dt";

import "datatables.net-dt/css/dataTables.dataTables.css";

/*
|--------------------------------------------------------------------------
| DATATABLES GLOBAL INIT
|--------------------------------------------------------------------------
*/
window.initDataTable = (selector, options = {}) => {
  return new DataTable(selector, {
    processing: true,
    serverSide: true,
    responsive: true,
    autoWidth: false,
    pageLength: 10,

    language: {
      processing: "Loading...",
      search: "Cari:",
      lengthMenu: "Tampilkan _MENU_ data",
      info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
      infoEmpty: "Tidak ada data",
      zeroRecords: "Data tidak ditemukan",
      emptyTable: "Tidak ada data tersedia",
    },

    ...options,
  });
};
