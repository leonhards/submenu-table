(function($) {
  "use strict";
  
	$(document).ready(function() {

    // Datatable for Make List
    var mytable = $('#mytable').DataTable({
      responsive: true,
      columnDefs: [{
        targets: 0,
        width: "5%",
        orderable: false
      },{
        targets: [0, -1],
        className: 'dt-body-center'
      }, {
        targets: -1,
        width: "15%",
        orderable: false
      }],
      order: [[ 1, 'asc' ]]
    });

    mytable.on( 'order.dt search.dt', function () {
      mytable.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
          cell.innerHTML = i + 1;
      } );
   } ).draw();

	} );
})(jQuery);