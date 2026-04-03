function nwmFormatCurrency(value) {
  return "$" + value.toFixed(2);
}

function nwmSanitizeNumericInput(input) {
  if (!input) {
    return;
  }

  var cleaned = input.value.replace(/[^0-9.]/g, "");
  var parts = cleaned.split(".");

  if (parts.length > 2) {
    cleaned = parts.shift() + "." + parts.join("");
  }

  input.value = cleaned;
}

function nwmUpdateRiskCalculator(calculator) {
  if (!calculator) {
    return;
  }

  var balance = parseFloat(calculator.querySelector("[data-nwm-balance]")?.value || "0");
  var riskPercent = parseFloat(calculator.querySelector("[data-nwm-risk]")?.value || "0");
  var entry = parseFloat(calculator.querySelector("[data-nwm-entry]")?.value || "0");
  var stop = parseFloat(calculator.querySelector("[data-nwm-stop]")?.value || "0");
  var target = parseFloat(calculator.querySelector("[data-nwm-target]")?.value || "0");
  var pointValue = parseFloat(calculator.querySelector("[data-nwm-point-value]")?.value || "0");

  var riskAmount = balance > 0 && riskPercent > 0 ? (balance * riskPercent) / 100 : 0;
  var stopDistance = entry > 0 && stop > 0 ? Math.abs(entry - stop) : 0;
  var rewardDistance = entry > 0 && target > 0 ? Math.abs(target - entry) : 0;
  var riskPerPoint = stopDistance > 0 ? riskAmount / stopDistance : 0;
  var positionSize = stopDistance > 0 && pointValue > 0 ? riskAmount / (stopDistance * pointValue) : 0;
  var rewardAmount = rewardDistance > 0 && pointValue > 0 && positionSize > 0 ? rewardDistance * pointValue * positionSize : 0;
  var riskReward = stopDistance > 0 && rewardDistance > 0 ? rewardDistance / stopDistance : 0;

  var riskAmountNode = calculator.querySelector("[data-nwm-risk-amount]");
  var stopDistanceNode = calculator.querySelector("[data-nwm-stop-distance]");
  var positionSizeNode = calculator.querySelector("[data-nwm-position-size]");
  var rewardAmountNode = calculator.querySelector("[data-nwm-reward-amount]");
  var riskRewardNode = calculator.querySelector("[data-nwm-rr]");
  var riskPerPointNode = calculator.querySelector("[data-nwm-risk-per-point]");

  if (riskAmountNode) {
    riskAmountNode.textContent = nwmFormatCurrency(riskAmount);
  }

  if (stopDistanceNode) {
    stopDistanceNode.textContent = stopDistance > 0 ? stopDistance.toFixed(5) : "0.00000";
  }

  if (positionSizeNode) {
    positionSizeNode.textContent = positionSize > 0 ? positionSize.toFixed(2) + " lots" : "0.00 lots";
  }

  if (rewardAmountNode) {
    rewardAmountNode.textContent = nwmFormatCurrency(rewardAmount);
  }

  if (riskRewardNode) {
    riskRewardNode.textContent = riskReward > 0 ? riskReward.toFixed(2) + "R" : "0.00R";
  }

  if (riskPerPointNode) {
    riskPerPointNode.textContent = nwmFormatCurrency(riskPerPoint);
  }
}

function nwmUpdatePositionCalculator(calculator) {
  if (!calculator) {
    return;
  }

  var riskAmount = parseFloat(calculator.querySelector("[data-nwm-position-risk]")?.value || "0");
  var stopDistance = parseFloat(calculator.querySelector("[data-nwm-position-stop]")?.value || "0");
  var pointValue = parseFloat(calculator.querySelector("[data-nwm-position-point-value]")?.value || "0");

  var positionSize = stopDistance > 0 && pointValue > 0 ? riskAmount / (stopDistance * pointValue) : 0;
  var riskPerPoint = stopDistance > 0 ? riskAmount / stopDistance : 0;
  var positionStopValue = positionSize > 0 && pointValue > 0 && stopDistance > 0 ? positionSize * pointValue * stopDistance : 0;

  var lotsNode = calculator.querySelector("[data-nwm-position-lots]");
  var perPointNode = calculator.querySelector("[data-nwm-position-per-point]");
  var stopValueNode = calculator.querySelector("[data-nwm-position-stop-value]");

  if (lotsNode) {
    lotsNode.textContent = positionSize > 0 ? positionSize.toFixed(2) + " lots" : "0.00 lots";
  }

  if (perPointNode) {
    perPointNode.textContent = nwmFormatCurrency(riskPerPoint);
  }

  if (stopValueNode) {
    stopValueNode.textContent = nwmFormatCurrency(positionStopValue);
  }
}

function nwmUpdatePipCalculator(calculator) {
  if (!calculator) {
    return;
  }

  var lots = parseFloat(calculator.querySelector("[data-nwm-pip-lots]")?.value || "0");
  var baseValue = parseFloat(calculator.querySelector("[data-nwm-pip-base-value]")?.value || "0");
  var distance = parseFloat(calculator.querySelector("[data-nwm-pip-distance]")?.value || "0");

  var pipValue = lots > 0 && baseValue > 0 ? lots * baseValue : 0;
  var totalMoveValue = pipValue > 0 && distance > 0 ? pipValue * distance : 0;
  var miniLots = lots > 0 ? lots * 100 : 0;

  var pipValueNode = calculator.querySelector("[data-nwm-pip-value]");
  var pipTotalNode = calculator.querySelector("[data-nwm-pip-total]");
  var pipMiniNode = calculator.querySelector("[data-nwm-pip-mini]");

  if (pipValueNode) {
    pipValueNode.textContent = nwmFormatCurrency(pipValue);
  }

  if (pipTotalNode) {
    pipTotalNode.textContent = nwmFormatCurrency(totalMoveValue);
  }

  if (pipMiniNode) {
    pipMiniNode.textContent = miniLots > 0 ? miniLots.toFixed(0) + " mini lots" : "0 mini lots";
  }
}

function nwmUpdateProfitCalculator(calculator) {
  if (!calculator) {
    return;
  }

  var balance = parseFloat(calculator.querySelector("[data-nwm-profit-balance]")?.value || "0");
  var entry = parseFloat(calculator.querySelector("[data-nwm-profit-entry]")?.value || "0");
  var target = parseFloat(calculator.querySelector("[data-nwm-profit-target]")?.value || "0");
  var stop = parseFloat(calculator.querySelector("[data-nwm-profit-stop]")?.value || "0");
  var lots = parseFloat(calculator.querySelector("[data-nwm-profit-lots]")?.value || "0");
  var pointValue = parseFloat(calculator.querySelector("[data-nwm-profit-point-value]")?.value || "0");

  var targetDistance = entry > 0 && target > 0 ? Math.abs(target - entry) : 0;
  var stopDistance = entry > 0 && stop > 0 ? Math.abs(entry - stop) : 0;
  var pipValue = lots > 0 && pointValue > 0 ? lots * pointValue : 0;
  var profitAmount = targetDistance > 0 && pipValue > 0 ? targetDistance * pipValue : 0;
  var riskAmount = stopDistance > 0 && pipValue > 0 ? stopDistance * pipValue : 0;
  var rewardRisk = profitAmount > 0 && riskAmount > 0 ? profitAmount / riskAmount : 0;
  var growth = balance > 0 && profitAmount > 0 ? (profitAmount / balance) * 100 : 0;

  var distanceNode = calculator.querySelector("[data-nwm-profit-distance]");
  var amountNode = calculator.querySelector("[data-nwm-profit-amount]");
  var rrNode = calculator.querySelector("[data-nwm-profit-rr]");
  var growthNode = calculator.querySelector("[data-nwm-profit-growth]");

  if (distanceNode) {
    distanceNode.textContent = targetDistance > 0 ? targetDistance.toFixed(5) : "0.00000";
  }

  if (amountNode) {
    amountNode.textContent = nwmFormatCurrency(profitAmount);
  }

  if (rrNode) {
    rrNode.textContent = rewardRisk > 0 ? rewardRisk.toFixed(2) + "R" : "0.00R";
  }

  if (growthNode) {
    growthNode.textContent = growth > 0 ? growth.toFixed(2) + "%" : "0.00%";
  }
}

function nwmActivateToolHubTab(hub, toolId) {
  if (!hub || !toolId) {
    return;
  }

  hub.querySelectorAll("[data-nwm-tool-tab]").forEach(function (tab) {
    var isActive = tab.getAttribute("data-nwm-tool-tab") === toolId;
    tab.classList.toggle("is-active", isActive);
    tab.setAttribute("aria-current", isActive ? "page" : "false");
  });

  hub.querySelectorAll("[data-nwm-tool-pane]").forEach(function (pane) {
    var isActive = pane.getAttribute("data-nwm-tool-pane") === toolId;
    pane.classList.toggle("is-active", isActive);
    pane.hidden = !isActive;
  });

  try {
    window.localStorage.setItem("nwmActiveTool", toolId);
  } catch (error) {
    // Ignore storage failures.
  }
}

function nwmGetToolFromLocation(hub) {
  if (!hub) {
    return "";
  }

  var pathSegments = window.location.pathname.replace(/\/+$/, "").split("/");
  var lastSegment = pathSegments[pathSegments.length - 1] || "";

  if (lastSegment && hub.querySelector('[data-nwm-tool-tab="' + lastSegment + '"]')) {
    return lastSegment;
  }

  var hash = window.location.hash ? window.location.hash.replace("#", "") : "";
  if (hash && hub.querySelector('[data-nwm-tool-tab="' + hash + '"]')) {
    return hash;
  }

  return "";
}

document.addEventListener("input", function (event) {
  var numericInput = event.target.closest("[data-nwm-numeric]");
  if (numericInput) {
    nwmSanitizeNumericInput(numericInput);
  }

  var calculator = event.target.closest("[data-nwm-risk-calculator]");
  if (calculator) {
    nwmUpdateRiskCalculator(calculator);
  }

  var positionCalculator = event.target.closest("[data-nwm-position-calculator]");
  if (positionCalculator) {
    nwmUpdatePositionCalculator(positionCalculator);
  }

  var pipCalculator = event.target.closest("[data-nwm-pip-calculator]");
  if (pipCalculator) {
    nwmUpdatePipCalculator(pipCalculator);
  }

  var profitCalculator = event.target.closest("[data-nwm-profit-calculator]");
  if (profitCalculator) {
    nwmUpdateProfitCalculator(profitCalculator);
  }
});

document.addEventListener("click", function (event) {
  var tab = event.target.closest("[data-nwm-tool-tab]");
  if (!tab) {
    return;
  }

  event.preventDefault();

  var hub = tab.closest("[data-nwm-tools-hub]");
  var toolId = tab.getAttribute("data-nwm-tool-tab");
  var href = tab.getAttribute("href");

  nwmActivateToolHubTab(hub, toolId);

  if (href && window.history && window.history.pushState) {
    window.history.pushState({ toolId: toolId }, "", href);
  }
});

document.addEventListener("keydown", function (event) {
  var tab = event.target.closest("[data-nwm-tool-tab]");
  if (!tab) {
    return;
  }

  if (event.key !== "ArrowRight" && event.key !== "ArrowLeft") {
    return;
  }

  var hub = tab.closest("[data-nwm-tools-hub]");
  var tabs = Array.from(hub.querySelectorAll("[data-nwm-tool-tab]"));
  var currentIndex = tabs.indexOf(tab);
  var nextIndex = event.key === "ArrowRight" ? currentIndex + 1 : currentIndex - 1;

  if (nextIndex < 0) {
    nextIndex = tabs.length - 1;
  }

  if (nextIndex >= tabs.length) {
    nextIndex = 0;
  }

  event.preventDefault();
  tabs[nextIndex].focus();
  nwmActivateToolHubTab(hub, tabs[nextIndex].getAttribute("data-nwm-tool-tab"));
});

document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll("[data-nwm-risk-calculator]").forEach(function (calculator) {
    nwmUpdateRiskCalculator(calculator);
  });

  document.querySelectorAll("[data-nwm-position-calculator]").forEach(function (calculator) {
    nwmUpdatePositionCalculator(calculator);
  });

  document.querySelectorAll("[data-nwm-pip-calculator]").forEach(function (calculator) {
    nwmUpdatePipCalculator(calculator);
  });

  document.querySelectorAll("[data-nwm-profit-calculator]").forEach(function (calculator) {
    nwmUpdateProfitCalculator(calculator);
  });

  document.querySelectorAll("[data-nwm-tools-hub]").forEach(function (hub) {
    var firstTab = hub.querySelector("[data-nwm-tool-tab]");
    var locationTool = nwmGetToolFromLocation(hub);
    var stored = "";

    try {
      stored = window.localStorage.getItem("nwmActiveTool") || "";
    } catch (error) {
      stored = "";
    }

    var requestedTab = locationTool ? hub.querySelector('[data-nwm-tool-tab="' + locationTool + '"]') : null;
    if (!requestedTab && stored) {
      requestedTab = hub.querySelector('[data-nwm-tool-tab="' + stored + '"]');
    }
    var targetTab = requestedTab || firstTab;

    if (targetTab) {
      nwmActivateToolHubTab(hub, targetTab.getAttribute("data-nwm-tool-tab"));
    }
  });
});

window.addEventListener("popstate", function () {
  document.querySelectorAll("[data-nwm-tools-hub]").forEach(function (hub) {
    var firstTab = hub.querySelector("[data-nwm-tool-tab]");
    var locationTool = nwmGetToolFromLocation(hub);
    var targetTab = locationTool ? hub.querySelector('[data-nwm-tool-tab="' + locationTool + '"]') : firstTab;

    if (targetTab) {
      nwmActivateToolHubTab(hub, targetTab.getAttribute("data-nwm-tool-tab"));
    }
  });
});
