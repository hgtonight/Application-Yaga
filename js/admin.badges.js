/* Copyright 2013 Zachary Doll */

// Poor mans cache
var Cache = {
  data: {},
  remove: function(key) {
    delete Cache.data[key];
  },
  exists: function(key) {
    return Cache.data.hasOwnProperty(key) && Cache.data[key] !== null;
  },
  get: function(key) {
    return Cache.data[key];
  },
  set: function(key, cachedData) {
    Cache.remove(key);
    Cache.data[key] = cachedData;
  }
};

jQuery(document).ready(function($) {
  
  $('#Badges').sortable({
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
      $('#Badges tbody tr:nth-child(odd)').removeClass('Alt');
      $('#Badges tbody tr:nth-child(even)').addClass('Alt');
      
      $.post(
        gdn.url('badge/sort.json'),
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

  // Store the current inputs in the form
  $(document).on('blur', '#Rule-Criteria input', function() {
    $(this).attr('value', $(this).val());
  });
  $(document).on('blur', '#Rule-Criteria select', function() {
    var currentValue = $(this).val();
    $(this).children('option').each(function() {$(this).removeAttr('selected'); });
    $(this).find("option[value='" + currentValue + "']")
      .attr('selected', 'selected')
      .prop('selected', true);
  });

  // This handles retrieving and displaying the different rule criteria forms
  $("form.Badge select[name='RuleClass']").focus(function() {
    // Save the current form to the current value's cache on focus
    var Rule = $(this).val();
    var RuleForm = $('#Rule-Criteria').html();
    var RuleDesc = $('#Rule-Description').html();
    Cache.set(Rule, {'Form' : RuleForm, 'Description' : RuleDesc});
  }).change(function() {
    // Grab the form from cache or ajax on change
    var NewRule = $(this).val();
    if (Cache.exists(NewRule)) {
      $('#Rule-Criteria').fadeOut(function() {
        $(this).html(Cache.get(NewRule).Form).fadeIn();
      });
      $('#Rule-Description').fadeOut(function() {
        $(this).html(Cache.get(NewRule).Description).fadeIn();
      });
    }
    else {
      // Grab the form via ajax
      var url = gdn.url('/rules/getcriteriaform/' + NewRule);
      $.ajax({
        url: url,
        global: false,
        type: 'GET',
        data: { 'DeliveryMethod' : 'JSON' },
        dataType: 'json',
        success: function(data) {
          Cache.set(NewRule, {'Form' : data.CriteriaForm, 'Description' : data.Description});
          $('#Rule-Criteria').fadeOut(function() {
            $(this).html(Cache.get(NewRule).Form).fadeIn();
          });
          $('#Rule-Description').fadeOut(function() {
            $(this).html(Cache.get(NewRule).Description).fadeIn();
          });
        },
        error: function(jqXHR) {
          gdn.informError(jqXHR);
        }
      });
    }
  });

});
