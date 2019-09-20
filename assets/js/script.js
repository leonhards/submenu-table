(function($) {
  "use strict";

  /**
   * Show the date of February correctly (every 4 years will be 29)
   */
  function isLeapYear(year) {
    year = parseInt(year);
    if (year % 4 != 0) {
        return false;
    } else if (year % 400 == 0) {
        return true;
    } else if (year % 100 == 0) {
        return false;
    } else {
        return true;
    }
  }
  
  function change_year(select, Days)
  {
    if( isLeapYear( $(select).val() ) )
    {
        Days[1] = 29;
    }
    else {
        Days[1] = 28;
    }

    if( parseInt($("#calendarMonth").val()) == 2 )
    {
      var day = $('#calendarDay');
      var val = $(day).val();
      $(day).empty();
      var option = '';
      for (var i=1; i <= Days[1]; i++) {
          option += '<option value="'+ String(i).padStart(2, '0') + '">' + i + '</option>';
      }
      $(day).append(option);
      if( val > Days[1] )
      {
        val = '01';
      }
      $(day).val(val);
    }
    var str = $('.calendarInput').val();
    var arr = str.split('/'); arr[2] = $(select).val();
    $('.calendarInput').val(arr.join('/'));
  }

  function change_month(select, Days) {
    if( isLeapYear( $('#calendarYear').val() ) )
    {
        Days[1] = 29;
    }
    else {
        Days[1] = 28;
    }

    var day = $('#calendarDay');
    var val = $(day).val();
    $(day).empty();
    var option = '';
    var month = parseInt( $(select).val() ) - 1;
    for (var i=1; i <= Days[ month ]; i++) {
        option += '<option value="'+ String(i).padStart(2, '0') + '">' + i + '</option>';
    }
    $(day).append(option);
    if( val > Days[ month ] )
    {
        val = '01';
    }
    $(day).val(val);
    var str = $('.calendarInput').val();
    // var arr = str.split('/'); arr[0] = $(select).val();  // English month version
    var arr = str.split('/'); arr[1] = $(select).val();  // Indonesia month version
    $('.calendarInput').val(arr.join('/'));
  }

  function change_day(select) {
    var str = $('.calendarInput').val();
    // var arr = str.split('/'); arr[1] = $(select).val();  // English day version
    var arr = str.split('/'); arr[0] = $(select).val();  // Indonesia day version
    $('.calendarInput').val(arr.join('/'));
  }

  // Datepicker with Select Options
  function DateofBirthSelectInit() {
    var d = new Date();

    var Days = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31]; // index => month [0-11]
    var Months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    // Day
    var option = '';
    var dd = String(d.getDate()).padStart(2, '0');
    for (var i = 1; i <= Days[0]; i++) {
      if( d.getDate() == i ) {
        option += '<option value="'+ String(i).padStart(2, '0') + '" selected>' + i + '</option>';
      } else {
        option += '<option value="'+ String(i).padStart(2, '0') + '">' + i + '</option>';
      }
    }
    $('#calendarDay').append(option);

    // Month
    var option = '';
    var mm = String(d.getMonth() + 1).padStart(2, '0'); //January is 0!
    for (var i = 1; i <= 12; i++) {
      if( ( d.getMonth() + 1 ) == i ) {
        option += '<option value="'+ String(i).padStart(2, '0') + '" selected>' + Months[i-1] + '</option>';
      } else {
        option += '<option value="'+ String(i).padStart(2, '0') + '">' + Months[i-1] + '</option>';
      }
    }
    $('#calendarMonth').append(option);

    // Year
    var option = '';
    var yyyy = d.getFullYear();
    for (var i = d.getFullYear(); i >= 2019; i--){
      if( ( d.getFullYear() ) == i ) {
        option += '<option value="'+ i + '" selected>' + i + '</option>';
      } else {
        option += '<option value="'+ i + '">' + i + '</option>';
      }
    }
    $('#calendarYear').append(option);

    $('#calendarMonth').change(function() {
      change_month(this, Days);
    });

    $('#calendarDay').change(function() {
      change_day(this);
    });

    $('#calendarYear').change(function() {
      change_year(this, Days);
    });
  }

  $(document).ready(function() {

    // Call function for calendar options
    DateofBirthSelectInit();

    // Set hidden input as current date
    var today = new Date();
    var current_date = String(today.getDate()).padStart(2, '0');
    var current_month = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var current_year = today.getFullYear();
    today = current_date + '/' + current_month + '/' + current_year;

    if( $('.calendarInput').length ) {
      if( $('.calendarInput').val() == '' ) {
        $('.calendarInput').val(today);
      } else {
        var str = $('.calendarInput').val();
        var arr = str.split('/');
        $('#calendarDay').val(arr[0]);
        $('#calendarMonth').val(arr[1]);
        $('#calendarYear').val(arr[2]);
      }
    }

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