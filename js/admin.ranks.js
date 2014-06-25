/* Copyright 2013 Zachary Doll */
jQuery(document).ready(function($) {
  $('#Ranks').sortable({
    axis: 'y',
    containment: 'parent',
    cursor: 'move',
    cursorAt: {left: '10px'},
    forcePlaceholderSize: true,
    items: 'tr',
    placeholder: 'Placeholder',
    opacity: .6,
    tolerance: 'pointer',
    update: function() {
      // Update the alt classes
      $('#Ranks tbody tr:nth-child(odd)').removeClass('Alt');
      $('#Ranks tbody tr:nth-child(even)').addClass('Alt');

      // Save the current sort method
      $.post(
        gdn.url('rank/sort.json'),
        {
          'SortArray': $('table.Sortable').sortable('toArray'),
          'TransientKey': gdn.definition('TransientKey')
        },
        function(response) {
          if (!response || !response.Result) {
            alert("Oops - Didn't save order properly");
          }
        }
      );
    },
    helper: function(e, ui) {
      // Preserve width of row
      ui.children().each(function() {
        $(this).width($(this).width());
      });
      return ui;
    }
  });
});
