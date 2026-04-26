function nwmFormatCurrency(value) {
  return "$" + value.toFixed(2);
}

function nwmFormatCurrencyWithSymbol(symbol, value) {
  return (symbol || "") + value.toFixed(2);
}

var nwmCalculatorTimers = new WeakMap();

function nwmQueueCalculatorUpdate(calculator, key, fn, delay) {
  if (!calculator || typeof fn !== "function") {
    return;
  }

  var timers = nwmCalculatorTimers.get(calculator) || {};
  var timeout = timers[key];

  if (timeout) {
    window.clearTimeout(timeout);
  }

  timers[key] = window.setTimeout(function () {
    fn(calculator);
  }, delay || 120);

  nwmCalculatorTimers.set(calculator, timers);
}

function nwmHasTools() {
  return !!document.querySelector(
    "[data-nwm-tools-hub]," +
    " [data-nwm-risk-calculator]," +
    " [data-nwm-position-calculator]," +
    " [data-nwm-pip-calculator]," +
    " [data-nwm-profit-calculator]," +
    " [data-nwm-compound-calculator]"
  );
}

var nwmToolsPresent = nwmHasTools();

function nwmGetCompoundPeriodLabel(frequency, period) {
  if (frequency === 12) {
    return "Month " + period;
  }

  if (frequency === 52) {
    return "Week " + period;
  }

  if (frequency === 365 || frequency === 360) {
    return "Day " + period;
  }

  if (frequency === 4) {
    return "Quarter " + period;
  }

  if (frequency === 2) {
    return "Half-Year " + period;
  }

  if (frequency === 1) {
    return "Year " + period;
  }

  return "Period " + period;
}

function nwmGetCompoundRowsForView(rows, frequency, totalMonths, view) {
  if (!rows.length || frequency <= 0 || view === "period") {
    return rows;
  }

  var targetCount = view === "yearly"
    ? Math.max(1, Math.ceil(totalMonths / 12))
    : Math.max(1, totalMonths);
  var summaryRows = [];
  var seenPeriods = {};

  for (var index = 1; index <= targetCount; index += 1) {
    var targetPeriod = view === "yearly"
      ? Math.ceil(index * frequency)
      : Math.ceil((index / 12) * frequency);
    var rowIndex = Math.min(rows.length - 1, Math.max(0, targetPeriod - 1));
    var row = rows[rowIndex];

    if (!row || seenPeriods[row.period]) {
      continue;
    }

    seenPeriods[row.period] = true;
    summaryRows.push({
      period: row.period,
      label: view === "yearly" ? "Year " + index : "Month " + index,
      balance: row.balance,
      contributions: row.contributions,
      profit: row.profit
    });
  }

  var lastRow = rows[rows.length - 1];

  if (lastRow && !seenPeriods[lastRow.period]) {
    summaryRows.push({
      period: lastRow.period,
      label: view === "yearly" ? "Final Year" : "Final Month",
      balance: lastRow.balance,
      contributions: lastRow.contributions,
      profit: lastRow.profit
    });
  }

  return summaryRows.length ? summaryRows : rows;
}

function nwmBuildCompoundShareUrl(calculator) {
  if (!calculator || typeof window === "undefined") {
    return "";
  }

  var url = new URL(window.location.href);
  var params = url.searchParams;
  var fields = {
    currency: "[data-nwm-compound-currency]",
    principal: "[data-nwm-compound-principal]",
    rate: "[data-nwm-compound-rate]",
    rate_period: "[data-nwm-compound-rate-period]",
    years: "[data-nwm-compound-years]",
    months: "[data-nwm-compound-months]",
    frequency: "[data-nwm-compound-frequency]",
    contribution: "[data-nwm-compound-contribution]",
    contribution_frequency: "[data-nwm-compound-contribution-frequency]",
    view: "[data-nwm-compound-view]",
    chart: "[data-nwm-compound-chart-type]"
  };

  Object.keys(fields).forEach(function (key) {
    var field = calculator.querySelector(fields[key]);
    var value = field ? String(field.value || "").trim() : "";

    if (value) {
      params.set(key, value);
    } else {
      params.delete(key);
    }
  });

  return url.toString();
}

function nwmPopulateCompoundCalculatorFromUrl(calculator) {
  if (!calculator || typeof window === "undefined") {
    return;
  }

  var params = new URLSearchParams(window.location.search);
  var fields = {
    currency: "[data-nwm-compound-currency]",
    principal: "[data-nwm-compound-principal]",
    rate: "[data-nwm-compound-rate]",
    rate_period: "[data-nwm-compound-rate-period]",
    years: "[data-nwm-compound-years]",
    months: "[data-nwm-compound-months]",
    frequency: "[data-nwm-compound-frequency]",
    contribution: "[data-nwm-compound-contribution]",
    contribution_frequency: "[data-nwm-compound-contribution-frequency]",
    view: "[data-nwm-compound-view]",
    chart: "[data-nwm-compound-chart-type]"
  };

  Object.keys(fields).forEach(function (key) {
    if (!params.has(key)) {
      return;
    }

    var field = calculator.querySelector(fields[key]);

    if (field) {
      field.value = params.get(key) || field.value;
    }
  });
}

function nwmSetCompoundShareStatus(calculator, message) {
  var statusNode = calculator?.querySelector("[data-nwm-compound-share-status]");

  if (!statusNode) {
    return;
  }

  statusNode.textContent = message || "";

  if (message) {
    window.setTimeout(function () {
      if (statusNode.textContent === message) {
        statusNode.textContent = "";
      }
    }, 2200);
  }
}

function nwmGetCompoundChartLabelIndexes(length) {
  if (length <= 1) {
    return { 0: true };
  }

  var indexes = {};
  indexes[0] = true;
  indexes[length - 1] = true;
  var steps = Math.min(5, length - 1);

  for (var index = 1; index < steps; index += 1) {
    indexes[Math.round((index * (length - 1)) / steps)] = true;
  }

  return indexes;
}

function nwmRenderCompoundLineChart(chartNode, rows, currency) {
  if (!chartNode) {
    return;
  }

  if (!rows.length) {
    chartNode.innerHTML = '<p class="nwm-tool-chart__empty">Enter your numbers to generate the projection chart.</p>';
    return;
  }

  var width = 720;
  var height = 260;
  var paddingLeft = 88;
  var paddingRight = 24;
  var paddingTop = 20;
  var paddingBottom = 42;
  var chartHeight = height - paddingTop - paddingBottom;
  var chartWidth = width - paddingLeft - paddingRight;
  var maxBalance = Math.max.apply(null, rows.map(function (row) { return row.balance; })) || 1;
  var minBalance = Math.min.apply(null, rows.map(function (row) { return row.balance; })) || 0;
  var balanceRange = Math.max(1, maxBalance - minBalance);

  var points = rows.map(function (row, index) {
    var ratioX = rows.length === 1 ? 0.5 : index / (rows.length - 1);
    var ratioY = (row.balance - minBalance) / balanceRange;
    var x = paddingLeft + ratioX * chartWidth;
    var y = paddingTop + chartHeight - ratioY * chartHeight;

    return {
      x: x,
      y: y,
      label: row.label,
      balance: row.balance
    };
  });

  var polyline = points.map(function (point) {
    return point.x.toFixed(2) + "," + point.y.toFixed(2);
  }).join(" ");
  var labelIndexes = nwmGetCompoundChartLabelIndexes(points.length);

  var gridLines = [0, 0.25, 0.5, 0.75, 1].map(function (step) {
    var y = paddingTop + chartHeight - step * chartHeight;
    var balance = minBalance + step * balanceRange;

    return (
        '<g class="nwm-tool-chart__gridline">' +
        '<line x1="' + paddingLeft + '" y1="' + y.toFixed(2) + '" x2="' + (width - paddingRight) + '" y2="' + y.toFixed(2) + '"></line>' +
        '<text x="8" y="' + (y + 4).toFixed(2) + '">' + nwmFormatCurrencyWithSymbol(currency, balance) + '</text>' +
      '</g>'
    );
  }).join("");

  var pointMarkup = points.map(function (point, index) {
    var anchor = index === points.length - 1 ? "end" : index === 0 ? "start" : "middle";
    var label = labelIndexes[index]
      ? '<text class="nwm-tool-chart__label" text-anchor="' + anchor + '" x="' + point.x.toFixed(2) + '" y="' + (height - 12) + '">' + point.label + '</text>'
      : "";
    var detailLabel = point.label.replace(/"/g, "&quot;");
    var detailBalance = nwmFormatCurrencyWithSymbol(currency, rows[index].balance).replace(/"/g, "&quot;");
    var detailContributions = nwmFormatCurrencyWithSymbol(currency, rows[index].contributions).replace(/"/g, "&quot;");
    var detailProfit = nwmFormatCurrencyWithSymbol(currency, rows[index].profit).replace(/"/g, "&quot;");

    return (
      '<g class="nwm-tool-chart__point-group">' +
        '<circle class="nwm-tool-chart__point" tabindex="0" cx="' + point.x.toFixed(2) + '" cy="' + point.y.toFixed(2) + '" r="4"' +
          ' data-nwm-compound-chart-point' +
          ' data-tooltip-label="' + detailLabel + '"' +
          ' data-tooltip-balance="' + detailBalance + '"' +
          ' data-tooltip-contributions="' + detailContributions + '"' +
          ' data-tooltip-profit="' + detailProfit + '"' +
        '></circle>' +
        label +
      '</g>'
    );
  }).join("");

  chartNode.innerHTML =
    '<svg class="nwm-tool-chart__svg" viewBox="0 0 ' + width + " " + height + '" role="img" aria-label="Compound growth line chart">' +
      gridLines +
      '<polyline class="nwm-tool-chart__line" fill="none" points="' + polyline + '"></polyline>' +
      pointMarkup +
    '</svg>' +
    '<div class="nwm-tool-chart__tooltip" data-nwm-compound-chart-tooltip hidden>' +
      '<strong class="nwm-tool-chart__tooltip-title"></strong>' +
      '<span class="nwm-tool-chart__tooltip-row" data-nwm-tooltip-balance></span>' +
      '<span class="nwm-tool-chart__tooltip-row" data-nwm-tooltip-contributions></span>' +
      '<span class="nwm-tool-chart__tooltip-row" data-nwm-tooltip-profit></span>' +
    '</div>';
}

function nwmRenderCompoundBarChart(chartNode, rows, currency) {
  if (!chartNode) {
    return;
  }

  if (!rows.length) {
    chartNode.innerHTML = '<p class="nwm-tool-chart__empty">Enter your numbers to generate the projection chart.</p>';
    return;
  }

  var sampleRows = rows.length > 10
    ? rows.filter(function (row, index) {
        return index === 0 || index === rows.length - 1 || ((index + 1) % Math.ceil(rows.length / 6) === 0);
      })
    : rows;
  var maxBalance = Math.max.apply(null, sampleRows.map(function (row) { return row.balance; })) || 1;

  chartNode.innerHTML = sampleRows.map(function (row) {
    var width = Math.max(8, (row.balance / maxBalance) * 100);

    return (
      '<div class="nwm-tool-chart__row">' +
        '<div class="nwm-tool-chart__meta">' +
          '<strong>' + row.label + '</strong>' +
          '<span>' + nwmFormatCurrencyWithSymbol(currency, row.balance) + '</span>' +
        '</div>' +
        '<div class="nwm-tool-chart__track">' +
          '<span class="nwm-tool-chart__fill" style="width:' + width.toFixed(2) + '%;"></span>' +
        '</div>' +
      '</div>'
    );
  }).join("");
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

function nwmNormalizePair(value) {
  return String(value || "")
    .toUpperCase()
    .replace(/[^A-Z]/g, "")
    .slice(0, 6);
}

function nwmSanitizePairInput(input) {
  if (!input) {
    return;
  }

  input.value = nwmNormalizePair(input.value);
}

function nwmGetForexContext(pairValue, accountCurrencyValue, referencePriceValue, conversionRateValue) {
  var pair = nwmNormalizePair(pairValue);
  var accountCurrency = String(accountCurrencyValue || "USD").toUpperCase().replace(/[^A-Z]/g, "").slice(0, 3) || "USD";
  var referencePrice = parseFloat(referencePriceValue || "0");
  var conversionRate = parseFloat(conversionRateValue || "0");
  var isValidPair = pair.length === 6;
  var baseCurrency = isValidPair ? pair.slice(0, 3) : "";
  var quoteCurrency = isValidPair ? pair.slice(3, 6) : "";
  var pipSize = quoteCurrency === "JPY" ? 0.01 : 0.0001;
  var contractUnits = 100000;
  var needsQuoteConversion = isValidPair && accountCurrency !== quoteCurrency && accountCurrency !== baseCurrency;

  function convertQuoteToAccount(amount) {
    if (amount <= 0 || !isValidPair) {
      return 0;
    }

    if (accountCurrency === quoteCurrency) {
      return amount;
    }

    if (accountCurrency === baseCurrency) {
      return referencePrice > 0 ? amount / referencePrice : 0;
    }

    return conversionRate > 0 ? amount * conversionRate : 0;
  }

  function getPipDistance(priceA, priceB) {
    var a = parseFloat(priceA || "0");
    var b = parseFloat(priceB || "0");

    if (a <= 0 || b <= 0 || !isValidPair || pipSize <= 0) {
      return 0;
    }

    return Math.abs(a - b) / pipSize;
  }

  function getPipValueForLots(lots) {
    var numericLots = parseFloat(lots || "0");

    if (numericLots <= 0 || !isValidPair || pipSize <= 0) {
      return 0;
    }

    var pipValueInQuote = pipSize * contractUnits * numericLots;
    return convertQuoteToAccount(pipValueInQuote);
  }

  return {
    pair: pair,
    accountCurrency: accountCurrency,
    baseCurrency: baseCurrency,
    quoteCurrency: quoteCurrency,
    referencePrice: referencePrice,
    conversionRate: conversionRate,
    pipSize: pipSize,
    isValidPair: isValidPair,
    needsQuoteConversion: needsQuoteConversion,
    getPipDistance: getPipDistance,
    getPipValueForLots: getPipValueForLots
  };
}

function nwmFormatAccountValue(currencyCode, value) {
  return (currencyCode || "USD") + " " + value.toFixed(2);
}

function nwmGetForexContextMessage(context) {
  if (!context.isValidPair) {
    return "Enter a six-letter forex pair like EURUSD or GBPJPY.";
  }

  if (context.accountCurrency === context.quoteCurrency) {
    return "Pip value is quoted directly in " + context.accountCurrency + " because it is the quote currency for " + context.pair + ".";
  }

  if (context.accountCurrency === context.baseCurrency) {
    return "Pip value is converted from " + context.quoteCurrency + " into " + context.accountCurrency + " using the reference price for " + context.pair + ".";
  }

  if (context.conversionRate > 0) {
    return "Pip value is converted from " + context.quoteCurrency + " into " + context.accountCurrency + " using your quote-to-account rate.";
  }

  return "Enter the quote-to-account rate to convert " + context.quoteCurrency + " pip values into " + context.accountCurrency + " for " + context.pair + ".";
}

function nwmUpdateForexContextUi(calculator, context) {
  if (!calculator) {
    return;
  }

  var contextNode = calculator.querySelector("[data-nwm-forex-context]");
  if (contextNode) {
    contextNode.textContent = nwmGetForexContextMessage(context);
  }

  var conversionField = calculator.querySelector("[data-nwm-forex-conversion-field]");
  var conversionInput = conversionField ? conversionField.querySelector("input") : null;

  if (conversionField) {
    conversionField.classList.toggle("is-hidden", !context.needsQuoteConversion);
  }

  if (conversionInput) {
    conversionInput.disabled = !context.needsQuoteConversion;
    if (!context.needsQuoteConversion) {
      conversionInput.value = "1.00000";
    }
  }
}

function nwmUpdateRiskCalculator(calculator) {
  if (!calculator) {
    return;
  }

  var context = nwmGetForexContext(
    calculator.querySelector("[data-nwm-risk-pair]")?.value,
    calculator.querySelector("[data-nwm-risk-account-currency]")?.value,
    calculator.querySelector("[data-nwm-risk-reference]")?.value || calculator.querySelector("[data-nwm-entry]")?.value,
    calculator.querySelector("[data-nwm-risk-conversion]")?.value
  );
  var balance = parseFloat(calculator.querySelector("[data-nwm-balance]")?.value || "0");
  var riskPercent = parseFloat(calculator.querySelector("[data-nwm-risk]")?.value || "0");
  var entry = parseFloat(calculator.querySelector("[data-nwm-entry]")?.value || "0");
  var stop = parseFloat(calculator.querySelector("[data-nwm-stop]")?.value || "0");
  var target = parseFloat(calculator.querySelector("[data-nwm-target]")?.value || "0");

  var riskAmount = balance > 0 && riskPercent > 0 ? (balance * riskPercent) / 100 : 0;
  var stopDistance = context.getPipDistance(entry, stop);
  var rewardDistance = context.getPipDistance(entry, target);
  var standardLotPipValue = context.getPipValueForLots(1);
  var positionSize = stopDistance > 0 && standardLotPipValue > 0 ? riskAmount / (stopDistance * standardLotPipValue) : 0;
  var pipValue = context.getPipValueForLots(positionSize);
  var rewardAmount = rewardDistance > 0 && pipValue > 0 ? rewardDistance * pipValue : 0;
  var riskReward = stopDistance > 0 && rewardDistance > 0 ? rewardDistance / stopDistance : 0;
  var riskPerPoint = stopDistance > 0 ? riskAmount / stopDistance : 0;

  nwmUpdateForexContextUi(calculator, context);

  var riskAmountNode = calculator.querySelector("[data-nwm-risk-amount]");
  var stopDistanceNode = calculator.querySelector("[data-nwm-stop-distance]");
  var positionSizeNode = calculator.querySelector("[data-nwm-position-size]");
  var pipValueNode = calculator.querySelector("[data-nwm-risk-pip-value]");
  var rewardAmountNode = calculator.querySelector("[data-nwm-reward-amount]");
  var riskRewardNode = calculator.querySelector("[data-nwm-rr]");
  var riskPerPointNode = calculator.querySelector("[data-nwm-risk-per-point]");

  if (riskAmountNode) {
    riskAmountNode.textContent = nwmFormatAccountValue(context.accountCurrency, riskAmount);
  }

  if (stopDistanceNode) {
    stopDistanceNode.textContent = stopDistance > 0 ? stopDistance.toFixed(1) + " pips" : "0.0 pips";
  }

  if (positionSizeNode) {
    positionSizeNode.textContent = positionSize > 0 ? positionSize.toFixed(2) + " lots" : "0.00 lots";
  }

  if (pipValueNode) {
    pipValueNode.textContent = nwmFormatAccountValue(context.accountCurrency, pipValue);
  }

  if (rewardAmountNode) {
    rewardAmountNode.textContent = nwmFormatAccountValue(context.accountCurrency, rewardAmount);
  }

  if (riskRewardNode) {
    riskRewardNode.textContent = riskReward > 0 ? riskReward.toFixed(2) + "R" : "0.00R";
  }

  if (riskPerPointNode) {
    riskPerPointNode.textContent = nwmFormatAccountValue(context.accountCurrency, riskPerPoint);
  }
}

function nwmUpdatePositionCalculator(calculator) {
  if (!calculator) {
    return;
  }

  var context = nwmGetForexContext(
    calculator.querySelector("[data-nwm-position-pair]")?.value,
    calculator.querySelector("[data-nwm-position-account-currency]")?.value,
    calculator.querySelector("[data-nwm-position-reference]")?.value,
    calculator.querySelector("[data-nwm-position-conversion]")?.value
  );
  var riskAmount = parseFloat(calculator.querySelector("[data-nwm-position-risk]")?.value || "0");
  var stopDistance = parseFloat(calculator.querySelector("[data-nwm-position-stop]")?.value || "0");
  var standardLotPipValue = context.getPipValueForLots(1);
  var positionSize = stopDistance > 0 && standardLotPipValue > 0 ? riskAmount / (stopDistance * standardLotPipValue) : 0;
  var pipValue = context.getPipValueForLots(positionSize);
  var positionStopValue = pipValue > 0 && stopDistance > 0 ? pipValue * stopDistance : 0;

  nwmUpdateForexContextUi(calculator, context);

  var lotsNode = calculator.querySelector("[data-nwm-position-lots]");
  var perPointNode = calculator.querySelector("[data-nwm-position-per-point]");
  var stopValueNode = calculator.querySelector("[data-nwm-position-stop-value]");

  if (lotsNode) {
    lotsNode.textContent = positionSize > 0 ? positionSize.toFixed(2) + " lots" : "0.00 lots";
  }

  if (perPointNode) {
    perPointNode.textContent = nwmFormatAccountValue(context.accountCurrency, pipValue);
  }

  if (stopValueNode) {
    stopValueNode.textContent = nwmFormatAccountValue(context.accountCurrency, positionStopValue);
  }
}

function nwmUpdatePipCalculator(calculator) {
  if (!calculator) {
    return;
  }

  var context = nwmGetForexContext(
    calculator.querySelector("[data-nwm-pip-pair]")?.value,
    calculator.querySelector("[data-nwm-pip-account-currency]")?.value,
    calculator.querySelector("[data-nwm-pip-reference]")?.value,
    calculator.querySelector("[data-nwm-pip-conversion]")?.value
  );
  var lots = parseFloat(calculator.querySelector("[data-nwm-pip-lots]")?.value || "0");
  var distance = parseFloat(calculator.querySelector("[data-nwm-pip-distance]")?.value || "0");

  var pipValue = context.getPipValueForLots(lots);
  var totalMoveValue = pipValue > 0 && distance > 0 ? pipValue * distance : 0;
  var miniLots = lots > 0 ? lots * 100 : 0;

  nwmUpdateForexContextUi(calculator, context);

  var pipValueNode = calculator.querySelector("[data-nwm-pip-value]");
  var pipTotalNode = calculator.querySelector("[data-nwm-pip-total]");
  var pipMiniNode = calculator.querySelector("[data-nwm-pip-mini]");

  if (pipValueNode) {
    pipValueNode.textContent = nwmFormatAccountValue(context.accountCurrency, pipValue);
  }

  if (pipTotalNode) {
    pipTotalNode.textContent = nwmFormatAccountValue(context.accountCurrency, totalMoveValue);
  }

  if (pipMiniNode) {
    pipMiniNode.textContent = miniLots > 0 ? miniLots.toFixed(0) + " mini lots" : "0 mini lots";
  }
}

function nwmUpdateProfitCalculator(calculator) {
  if (!calculator) {
    return;
  }

  var context = nwmGetForexContext(
    calculator.querySelector("[data-nwm-profit-pair]")?.value,
    calculator.querySelector("[data-nwm-profit-account-currency]")?.value,
    calculator.querySelector("[data-nwm-profit-reference]")?.value || calculator.querySelector("[data-nwm-profit-entry]")?.value,
    calculator.querySelector("[data-nwm-profit-conversion]")?.value
  );
  var balance = parseFloat(calculator.querySelector("[data-nwm-profit-balance]")?.value || "0");
  var entry = parseFloat(calculator.querySelector("[data-nwm-profit-entry]")?.value || "0");
  var target = parseFloat(calculator.querySelector("[data-nwm-profit-target]")?.value || "0");
  var stop = parseFloat(calculator.querySelector("[data-nwm-profit-stop]")?.value || "0");
  var lots = parseFloat(calculator.querySelector("[data-nwm-profit-lots]")?.value || "0");

  var targetDistance = context.getPipDistance(entry, target);
  var stopDistance = context.getPipDistance(entry, stop);
  var pipValue = context.getPipValueForLots(lots);
  var profitAmount = targetDistance > 0 && pipValue > 0 ? targetDistance * pipValue : 0;
  var riskAmount = stopDistance > 0 && pipValue > 0 ? stopDistance * pipValue : 0;
  var rewardRisk = profitAmount > 0 && riskAmount > 0 ? profitAmount / riskAmount : 0;
  var growth = balance > 0 && profitAmount > 0 ? (profitAmount / balance) * 100 : 0;

  nwmUpdateForexContextUi(calculator, context);

  var distanceNode = calculator.querySelector("[data-nwm-profit-distance]");
  var amountNode = calculator.querySelector("[data-nwm-profit-amount]");
  var rrNode = calculator.querySelector("[data-nwm-profit-rr]");
  var growthNode = calculator.querySelector("[data-nwm-profit-growth]");
  var pipValueNode = calculator.querySelector("[data-nwm-profit-pip-value]");

  if (distanceNode) {
    distanceNode.textContent = targetDistance > 0 ? targetDistance.toFixed(1) + " pips" : "0.0 pips";
  }

  if (amountNode) {
    amountNode.textContent = nwmFormatAccountValue(context.accountCurrency, profitAmount);
  }

  if (rrNode) {
    rrNode.textContent = rewardRisk > 0 ? rewardRisk.toFixed(2) + "R" : "0.00R";
  }

  if (growthNode) {
    growthNode.textContent = growth > 0 ? growth.toFixed(2) + "%" : "0.00%";
  }

  if (pipValueNode) {
    pipValueNode.textContent = nwmFormatAccountValue(context.accountCurrency, pipValue);
  }
}

function nwmUpdateCompoundCalculator(calculator) {
  if (!calculator) {
    return;
  }

  var currency = calculator.querySelector("[data-nwm-compound-currency]")?.value || "$";
  var principal = parseFloat(calculator.querySelector("[data-nwm-compound-principal]")?.value || "0");
  var contribution = parseFloat(calculator.querySelector("[data-nwm-compound-contribution]")?.value || "0");
  var percentage = parseFloat(calculator.querySelector("[data-nwm-compound-rate]")?.value || "0");
  var ratePeriod = calculator.querySelector("[data-nwm-compound-rate-period]")?.value || "monthly";
  var years = parseFloat(calculator.querySelector("[data-nwm-compound-years]")?.value || "0");
  var months = parseFloat(calculator.querySelector("[data-nwm-compound-months]")?.value || "0");
  var frequency = parseFloat(calculator.querySelector("[data-nwm-compound-frequency]")?.value || "12");
  var contributionFrequency = parseFloat(calculator.querySelector("[data-nwm-compound-contribution-frequency]")?.value || "12");
  var view = calculator.querySelector("[data-nwm-compound-view]")?.value || "monthly";
  var chartType = calculator.querySelector("[data-nwm-compound-chart-type]")?.value || "line";

  var periodsPerYearMap = {
    daily: 365,
    weekly: 52,
    monthly: 12,
    yearly: 1
  };

  var ratePeriodsPerYear = periodsPerYearMap[ratePeriod] || 12;
  var totalYears = Math.max(0, years) + Math.max(0, months) / 12;
  var totalMonths = Math.max(1, Math.round(totalYears * 12));
  var totalCompoundPeriods = totalYears > 0 && frequency > 0 ? Math.floor(totalYears * frequency + 0.0001) : 0;
  var baseRate = percentage > 0 ? percentage / 100 : 0;
  var compoundRate = baseRate > 0 && frequency > 0
    ? Math.pow(1 + baseRate, ratePeriodsPerYear / frequency) - 1
    : 0;
  var contributionInterval = contribution > 0 && contributionFrequency > 0 && frequency > 0
    ? Math.max(1, Math.round(frequency / contributionFrequency))
    : 0;
  var endingBalance = principal > 0 ? principal : 0;
  var addedContributions = 0;
  var breakdownRows = [];

  for (var period = 1; period <= totalCompoundPeriods; period += 1) {
    if (compoundRate > 0) {
      endingBalance *= 1 + compoundRate;
    }

    if (contributionInterval > 0 && period % contributionInterval === 0) {
      endingBalance += contribution;
      addedContributions += contribution;
    }

    breakdownRows.push({
      period: period,
      label: nwmGetCompoundPeriodLabel(frequency, period),
      balance: endingBalance,
      contributions: principal + addedContributions,
      profit: endingBalance - (principal + addedContributions)
    });
  }

  var totalContributions = principal + addedContributions;
  var interestEarned = endingBalance - totalContributions;
  var growthMultiple = principal > 0 ? endingBalance / principal : 0;

  var endingNode = calculator.querySelector("[data-nwm-compound-ending]");
  var contributionsNode = calculator.querySelector("[data-nwm-compound-contributions]");
  var interestNode = calculator.querySelector("[data-nwm-compound-interest]");
  var multipleNode = calculator.querySelector("[data-nwm-compound-multiple]");
  var profitNode = calculator.querySelector("[data-nwm-compound-profit]");
  var breakdownNode = calculator.querySelector("[data-nwm-compound-breakdown]");
  var chartNode = calculator.querySelector("[data-nwm-compound-chart]");
  var displayedRows = nwmGetCompoundRowsForView(breakdownRows, frequency, totalMonths, view);

  if (endingNode) {
    endingNode.textContent = nwmFormatCurrencyWithSymbol(currency, endingBalance);
  }

  if (contributionsNode) {
    contributionsNode.textContent = nwmFormatCurrencyWithSymbol(currency, totalContributions);
  }

  if (interestNode) {
    interestNode.textContent = nwmFormatCurrencyWithSymbol(currency, interestEarned);
  }

  if (multipleNode) {
    multipleNode.textContent = totalCompoundPeriods > 0 ? String(totalCompoundPeriods) : "0";
  }

  if (profitNode) {
    profitNode.textContent = compoundRate > 0 ? (compoundRate * 100).toFixed(2) + "%" : "0.00%";
  }

  if (breakdownNode) {
    if (!displayedRows.length) {
      breakdownNode.innerHTML = '<tr><td colspan="4">Enter your numbers to see the projection.</td></tr>';
    } else {
      breakdownNode.innerHTML = displayedRows
        .map(function (row) {
          return (
            "<tr>" +
              "<td>" + row.label + "</td>" +
              "<td>" + nwmFormatCurrencyWithSymbol(currency, row.balance) + "</td>" +
              "<td>" + nwmFormatCurrencyWithSymbol(currency, row.contributions) + "</td>" +
              "<td>" + nwmFormatCurrencyWithSymbol(currency, row.profit) + "</td>" +
            "</tr>"
          );
        })
        .join("");
    }
  }

  if (chartType === "bar") {
    nwmRenderCompoundBarChart(chartNode, displayedRows, currency);
  } else {
    nwmRenderCompoundLineChart(chartNode, displayedRows, currency);
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

if (nwmToolsPresent) {
  document.addEventListener("input", function (event) {
    var numericInput = event.target.closest("[data-nwm-numeric]");
    if (numericInput) {
      nwmSanitizeNumericInput(numericInput);
    }

    if (
      event.target.matches("[data-nwm-risk-pair], [data-nwm-position-pair], [data-nwm-pip-pair], [data-nwm-profit-pair]")
    ) {
      nwmSanitizePairInput(event.target);
    }

    var calculator = event.target.closest("[data-nwm-risk-calculator]");
    if (calculator) {
      nwmQueueCalculatorUpdate(calculator, "risk", nwmUpdateRiskCalculator);
    }

    var positionCalculator = event.target.closest("[data-nwm-position-calculator]");
    if (positionCalculator) {
      nwmQueueCalculatorUpdate(positionCalculator, "position", nwmUpdatePositionCalculator);
    }

    var pipCalculator = event.target.closest("[data-nwm-pip-calculator]");
    if (pipCalculator) {
      nwmQueueCalculatorUpdate(pipCalculator, "pip", nwmUpdatePipCalculator);
    }

    var profitCalculator = event.target.closest("[data-nwm-profit-calculator]");
    if (profitCalculator) {
      nwmQueueCalculatorUpdate(profitCalculator, "profit", nwmUpdateProfitCalculator);
    }

    var compoundCalculator = event.target.closest("[data-nwm-compound-calculator]");
    if (compoundCalculator) {
      nwmQueueCalculatorUpdate(compoundCalculator, "compound", nwmUpdateCompoundCalculator, 160);
    }
  });

  document.addEventListener("change", function (event) {
    var riskCalculator = event.target.closest("[data-nwm-risk-calculator]");
    if (riskCalculator) {
      nwmQueueCalculatorUpdate(riskCalculator, "risk", nwmUpdateRiskCalculator, 60);
    }

    var positionCalculator = event.target.closest("[data-nwm-position-calculator]");
    if (positionCalculator) {
      nwmQueueCalculatorUpdate(positionCalculator, "position", nwmUpdatePositionCalculator, 60);
    }

    var pipCalculator = event.target.closest("[data-nwm-pip-calculator]");
    if (pipCalculator) {
      nwmQueueCalculatorUpdate(pipCalculator, "pip", nwmUpdatePipCalculator, 60);
    }

    var profitCalculator = event.target.closest("[data-nwm-profit-calculator]");
    if (profitCalculator) {
      nwmQueueCalculatorUpdate(profitCalculator, "profit", nwmUpdateProfitCalculator, 60);
    }

    var compoundCalculator = event.target.closest("[data-nwm-compound-calculator]");
    if (compoundCalculator) {
      nwmQueueCalculatorUpdate(compoundCalculator, "compound", nwmUpdateCompoundCalculator, 80);
    }
  });
}

if (nwmToolsPresent) {
document.addEventListener("click", function (event) {
  var presetButton = event.target.closest("[data-nwm-compound-preset]");
  if (presetButton) {
    var calculator = presetButton.closest("[data-nwm-compound-calculator]");
    if (calculator) {
      try {
        var preset = JSON.parse(presetButton.getAttribute("data-nwm-compound-preset") || "{}");

        var principal = calculator.querySelector("[data-nwm-compound-principal]");
        var contribution = calculator.querySelector("[data-nwm-compound-contribution]");
        var rate = calculator.querySelector("[data-nwm-compound-rate]");
        var ratePeriod = calculator.querySelector("[data-nwm-compound-rate-period]");
        var years = calculator.querySelector("[data-nwm-compound-years]");
        var months = calculator.querySelector("[data-nwm-compound-months]");
        var frequency = calculator.querySelector("[data-nwm-compound-frequency]");
        var contributionFrequency = calculator.querySelector("[data-nwm-compound-contribution-frequency]");
        var currency = calculator.querySelector("[data-nwm-compound-currency]");
        var view = calculator.querySelector("[data-nwm-compound-view]");

        if (principal && preset.principal !== undefined) principal.value = preset.principal;
        if (contribution && preset.contribution !== undefined) contribution.value = preset.contribution;
        if (rate && preset.rate !== undefined) rate.value = preset.rate;
        if (ratePeriod && preset.rate_period !== undefined) ratePeriod.value = preset.rate_period;
        if (years && preset.years !== undefined) years.value = preset.years;
        if (months && preset.months !== undefined) months.value = preset.months;
        if (frequency && preset.frequency !== undefined) frequency.value = preset.frequency;
        if (contributionFrequency && preset.contribution_frequency !== undefined) contributionFrequency.value = preset.contribution_frequency;
        if (currency && preset.currency !== undefined) currency.value = preset.currency;
        if (view && preset.view !== undefined) view.value = preset.view;

        nwmUpdateCompoundCalculator(calculator);
      } catch (error) {
        // Ignore malformed preset payloads.
      }
    }

    return;
  }

  var shareButton = event.target.closest("[data-nwm-compound-share]");
  if (shareButton) {
    var shareCalculator = shareButton.closest("[data-nwm-compound-calculator]");
    var shareUrl = nwmBuildCompoundShareUrl(shareCalculator);
    var shareTitle = "Forex Compound Calculator Scenario";
    var shareText = "Check this forex compound projection.";

    if (!shareCalculator || !shareUrl) {
      return;
    }

    if (navigator.share) {
      navigator.share({
        title: shareTitle,
        text: shareText,
        url: shareUrl
      }).then(function () {
        nwmSetCompoundShareStatus(shareCalculator, "Shared.");
      }).catch(function () {
        // Ignore dismissed share sheets.
      });

      return;
    }

    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(shareUrl).then(function () {
        nwmSetCompoundShareStatus(shareCalculator, "Link copied.");
      }).catch(function () {
        nwmSetCompoundShareStatus(shareCalculator, shareUrl);
      });

      return;
    }

    nwmSetCompoundShareStatus(shareCalculator, shareUrl);
    return;
  }

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
}

if (nwmToolsPresent) {
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
}

function nwmShowCompoundChartTooltip(point, event) {
  var chart = point.closest("[data-nwm-compound-chart]");
  var tooltip = chart?.querySelector("[data-nwm-compound-chart-tooltip]");

  if (!chart || !tooltip) {
    return;
  }

  var titleNode = tooltip.querySelector(".nwm-tool-chart__tooltip-title");
  var balanceNode = tooltip.querySelector("[data-nwm-tooltip-balance]");
  var contributionsNode = tooltip.querySelector("[data-nwm-tooltip-contributions]");
  var profitNode = tooltip.querySelector("[data-nwm-tooltip-profit]");

  if (titleNode) {
    titleNode.textContent = point.getAttribute("data-tooltip-label") || "";
  }

  if (balanceNode) {
    balanceNode.textContent = "Balance: " + (point.getAttribute("data-tooltip-balance") || "");
  }

  if (contributionsNode) {
    contributionsNode.textContent = "Contributions: " + (point.getAttribute("data-tooltip-contributions") || "");
  }

  if (profitNode) {
    profitNode.textContent = "Profit: " + (point.getAttribute("data-tooltip-profit") || "");
  }

  var chartRect = chart.getBoundingClientRect();
  var sourceRect = event ? { left: event.clientX, top: event.clientY } : point.getBoundingClientRect();
  var left = sourceRect.left - chartRect.left + 14;
  var top = sourceRect.top - chartRect.top - 14;

  tooltip.hidden = false;
  tooltip.style.left = left + "px";
  tooltip.style.top = top + "px";
}

function nwmHideCompoundChartTooltip(point) {
  var chart = point.closest("[data-nwm-compound-chart]");
  var tooltip = chart?.querySelector("[data-nwm-compound-chart-tooltip]");

  if (tooltip) {
    tooltip.hidden = true;
  }
}

if (nwmToolsPresent) {
document.addEventListener("mousemove", function (event) {
  var point = event.target.closest("[data-nwm-compound-chart-point]");

  if (point) {
    nwmShowCompoundChartTooltip(point, event);
  }
});
}

if (nwmToolsPresent) {
document.addEventListener("mouseleave", function (event) {
  var point = event.target.closest ? event.target.closest("[data-nwm-compound-chart-point]") : null;

  if (point) {
    nwmHideCompoundChartTooltip(point);
  }
}, true);
}

if (nwmToolsPresent) {
document.addEventListener("focusin", function (event) {
  var point = event.target.closest("[data-nwm-compound-chart-point]");

  if (point) {
    nwmShowCompoundChartTooltip(point);
  }
});
}

if (nwmHasTools()) {
document.addEventListener("focusout", function (event) {
  var point = event.target.closest("[data-nwm-compound-chart-point]");

  if (point) {
    nwmHideCompoundChartTooltip(point);
  }
});
}

document.addEventListener("DOMContentLoaded", function () {
  if (!nwmToolsPresent) {
    return;
  }

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

  document.querySelectorAll("[data-nwm-compound-calculator]").forEach(function (calculator) {
    nwmPopulateCompoundCalculatorFromUrl(calculator);
    nwmUpdateCompoundCalculator(calculator);
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

if (nwmToolsPresent) {
  window.addEventListener("popstate", function () {
    document.querySelectorAll("[data-nwm-tools-hub]").forEach(function (hub) {
      var firstTab = hub.querySelector("[data-nwm-tool-tab]");
      var locationTool = nwmGetToolFromLocation(hub);
      var targetTab = locationTool ? hub.querySelector('[data-nwm-tool-tab="' + locationTool + '"]') : firstTab;

      if (targetTab) {
        nwmActivateToolHubTab(hub, targetTab.getAttribute("data-nwm-tool-tab"));
      }
    });

    document.querySelectorAll("[data-nwm-compound-calculator]").forEach(function (calculator) {
      nwmPopulateCompoundCalculatorFromUrl(calculator);
      nwmUpdateCompoundCalculator(calculator);
    });
  });
}
