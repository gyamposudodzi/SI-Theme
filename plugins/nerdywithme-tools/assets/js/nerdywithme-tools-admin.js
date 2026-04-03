(function ($) {
  function setToolOrder($list, $input) {
    if (!$list.length || !$input.length) {
      return;
    }

    var order = $list
      .find("[data-tool-id]")
      .map(function () {
        return $(this).attr("data-tool-id");
      })
      .get()
      .join(",");

    $input.val(order);
  }

  $(function () {
    var $tabs = $(".nwm-tools-admin__tab");
    var $panels = $(".nwm-tools-admin__panel");

    $tabs.on("click", function () {
      var target = $(this).attr("data-nwm-admin-tab");

      $tabs.removeClass("is-active");
      $(this).addClass("is-active");

      $panels.removeClass("is-active");
      $panels.filter('[data-nwm-admin-panel="' + target + '"]').addClass("is-active");
    });

    var $sortable = $("[data-nwm-sortable-tools]");
    var $orderInput = $("[data-nwm-tool-order-input]");

    if ($sortable.length && $.fn.sortable) {
      $sortable.sortable({
        handle: ".nwm-tools-admin__drag",
        placeholder: "nwm-tools-admin__card-placeholder",
        update: function () {
          setToolOrder($sortable, $orderInput);
        }
      });

      setToolOrder($sortable, $orderInput);
    }
  });
})(jQuery);
