require(['core/first', 'jquery', 'jqueryui', 'core/ajax'], function(core, $, bootstrap, ajax) {


  $(document).ready(function() {

    var params = {};
    window.location.search
      .replace(/[?&]+([^=&]+)=([^&]*)/gi, function(str, key, value) {
        params[key] = value;
      });

    // set default value for dropdowns
    if (params['month']) {
      $('#month option[value=' + params['month'] + ']').attr('selected', 'selected');
    }
    if (params['year']) {
      $('#year option[value=' + params['year'] + ']').attr('selected', 'selected');
    }
    $(document).on('keypress', function(e) {
      if (e.which == 13) {
        searchusers();
      }
    });

    $('#search').click(function() {
      searchusers();
    });

    function searchusers() {
      console.log('search users');
      window.open("/local/staffmanager/index.php?month=" + $('#month').val() + "&year=" + $('#year').val(), '_self');
    }
  });
});