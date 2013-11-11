/* Copyright 2013 Zachary Doll */
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
  // TODO: Save form inputs to cache as well as the elements
  $('form.Badge select').focus(function() {
    // Update the cache before the change
    var Rule = $(this).val();
    var FormHtml = $('#Rule-Criteria').html();
    if(!Cache.exists(Rule)) {
      Cache.set(Rule, FormHtml);
    }
  }).change(function() {
    
  // Grab the form from cache or ajax
    var NewRule = $(this).val();
    if (Cache.exists(NewRule)) {
      $('#Rule-Criteria').fadeOut(function() {
        $(this).html(Cache.get(NewRule)).fadeIn();
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
          Cache.set(NewRule, data.CriteriaForm);
          $('#Rule-Criteria').fadeOut(function() {
            $(this).html(Cache.get(NewRule)).fadeIn();
          });
        },
        error: function(jqXHR) {
          gdn.informError(jqXHR);
        }     
      });
    }
  });
});
