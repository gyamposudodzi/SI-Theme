(function ($) {
  function updateAdCardMode($card) {
    if (!$card.length) {
      return;
    }

    var mode = $card.find("[data-nwm-ad-mode]").val() || "markup";
    var showMarkup = mode === "markup";
    var showManaged = mode === "promo" || mode === "image";

    $card.find('[data-nwm-ad-group="markup"]').toggleClass("is-hidden", !showMarkup);
    $card.find('[data-nwm-ad-group="managed"]').toggleClass("is-hidden", !showManaged);
  }

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

    var mediaFrame = null;

    $("[data-nwm-ad-card]").each(function () {
      updateAdCardMode($(this));
    });

    $(document).on("change", "[data-nwm-ad-mode]", function () {
      updateAdCardMode($(this).closest("[data-nwm-ad-card]"));
    });

    $(document).on("click", "[data-nwm-media-open]", function (event) {
      event.preventDefault();

      var $button = $(this);
      var $field = $button.siblings("[data-nwm-media-target]");

      if (!$field.length || typeof wp === "undefined" || !wp.media) {
        return;
      }

      mediaFrame = wp.media({
        title: "Choose ad image",
        button: {
          text: "Use this image"
        },
        library: {
          type: "image"
        },
        multiple: false
      });

      mediaFrame.on("select", function () {
        var attachment = mediaFrame.state().get("selection").first().toJSON();
        $field.val(attachment.url).trigger("change");
      });

      mediaFrame.open();
    });
  });
})(jQuery);
