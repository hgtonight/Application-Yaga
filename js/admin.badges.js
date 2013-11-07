/* Copyright 2013 Zachary Doll */
jQuery(document).ready(function($) {
  $('form.Badge select').change(function() {
    var Option = $(this).val();
    alert(Option + 'was selected!');
  });
});
