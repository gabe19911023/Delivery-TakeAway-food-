(function ($) {
    $(document).on('click', '.print-receipt', function (e) {
        onResizeCanvas('receipt-container-print', false);
    });
    $(document).on('click', '.btn-accept', function (e) {
        onResizeCanvas('receipt-container-details', false);
    });
    var timer = setInterval(function () {
        // if(counter>=10){
        // clearInterval(timer)
        // }
        if ($('#print_modal').hasClass('show')) {
            if ($('#print_modal').attr('data-flag') == '0') {
                onResizeCanvas('receipt-container-print', true);
                $('#print_modal').attr('data-flag', '1');
            }
        } else {
            $('#print_modal').attr('data-flag', '0');
        }
    }, 1000);

    function onDrawText() {
        var canvas = document.getElementById('canvasPaper');

        if (canvas.getContext) {
            var context = canvas.getContext('2d');

            var x = parseInt(0);
            var y = parseInt(0);
            var size = parseInt(32);

            //      context.textAlign    = 'start';
            context.textBaseline = 'top';

            var font = '';

            // if (document.getElementById('bold')  .checked) font += 'bold ';
            // if (document.getElementById('italic').checked) font += 'italic ';

            font += size.toString() + 'px ';

            font += 'Arial';

            context.font = font;

            context.fillText('Tzatzikis', x, y);
        }
    }

    function onResizeCanvas(className, isShow) {
        var canvasPaper = 'canvasPaper';
        if (isShow)
            canvasPaper = 'showCanvasPaper';
        var canvas = document.getElementById(canvasPaper);

        if (canvas.getContext) {
            var context = canvas.getContext('2d');
            switch ('inch3') {
                case 'inch2':
                    canvas.width = 384;
                    canvas.height = 213;
                    break;
                case 'inch3DotImpact':
                    canvas.width = 576;
                    canvas.height = 320;
                    break;
                default:
                    canvas.width = 600;
                    canvas.height = 1600;
                    break;
                case 'inch4':
                    canvas.width = 832;
                    canvas.height = 476;
                    break;
            }
            document.getElementById(canvasPaper).style.width = "700px";
            var element = $('.' + className + '');
            context.font = ("bolder 48px Arial");
            context.textAlign = "center";
            context.fillText(element.find('#merchant_name').text().toUpperCase(), 300, 40);
            context.font = ("40px Arial");
            context.fillText("Tel. " + element.find('#merchant_phone').text(), 300, 80);
            var pageHeight = 80;
            context.font = ("28px Arial");
            if (element.find('#merchant_address').text() != '') {
                let array = element.find('#merchant_address').text().split(',');
                for (var i = array.length - 1; i >= 0; i--) {
                    pageHeight += 40;
                    context.fillText(array[i], 300, pageHeight);
                }
            }
            context.font = ("1px Arial");
            pageHeight += 1;
            context.fillText("", 1, pageHeight);
            context.font = ("40px Arial");
            context.textAlign = "left";
            pageHeight += 40;
            context.fillText("---------------------------------------------------------------------", 15, pageHeight);
            context.textAlign = "center";
            context.font = ("bolder 48px Arial");
            pageHeight += 40;
            context.fillText($(document).find('#order_type').text().toUpperCase(), 300, pageHeight);
            context.font = ("40px Arial");
            pageHeight += 40;
            context.fillText($(document).find('#order_time').text().replace('Place on ', ''), 300, pageHeight);
            context.font = ("40px Arial");
            context.textAlign = "left";
            pageHeight += 40;
            context.fillText("---------------------------------------------------------------------", 15, pageHeight);
            context.font = ("1px Arial");
            pageHeight += 40;
            context.fillText("", 1, pageHeight);
            let startBody = pageHeight;
            var text = '';
            $('.' + className + ' .items-details .items-list').each(function (i, obj) {
                text = $(obj).find('b').text();
                if ($(obj).hasClass('addon')) {
                    $(obj).find('.sub_item_name').each(function (ii, obj2) {
                        text = $(this).text();
                        if (text != '') {
                            text = '+ ' + $(this).text();
                            context.font = ("28px Arial");
                            context.fillText(text, 15, pageHeight);
                            pageHeight += 40;
                        }
                    });
                    if ($(obj).find('.special_instructions').text() != '') {
                        text = $(obj).find('.special_instructions').text().replace('&amp;', '&');
                        if (text.length > 42) {
                            text = text.substring(0, 42) + " ...";
                        }
                        pageHeight -= 10;
                        context.font = ("24px Arial");
                        context.fillText(text, 15, pageHeight);
                        pageHeight += 40;
                    }
                    text = '';
                } else if (text != '') {
                    context.font = ("32px Arial");
                }
                if (text != '') {
                    text = text.replace('&amp;', '&');
                    if (text.length > 28) {
                        text = text.substring(0, 28) + " ...";
                    }
                    context.fillText(text, 15, pageHeight);
                    pageHeight += 40;
                }
            });
            // context.font = ("32px Arial");
            // context.fillText("1 x Product",15,360);
            // context.font = ("32px Arial");
            // context.fillText("2 x Product",15,400);
            // context.font = ("32px Arial");
            // context.fillText("1 x Product",15,440);
            // context.font = ("24px Arial");
            // context.fillText("+ Ingredient 1",35,480);
            // context.font = ("24px Arial");
            // context.fillText("+ Ingredient 2",35,520);
            context.font = ("1px Arial");
            context.fillText("", 1, pageHeight);
            pageHeight += 40;
            context.font = ("40px Arial");
            context.fillText("---------------------------------------------------------------------", 15, pageHeight);
            pageHeight += 40;

            $('.' + className + ' .summary .summary-items').each(function (i, obj) {
                text = $(obj).find('.summary-heading').text();
                context.font = ("32px Arial");
                context.fillText(text, 15, pageHeight);
                pageHeight += 40;
            });

            // context.font = ("32px Arial");
            // context.fillText("Service charge (5%);",15,600);
            // context.font = ("32px Arial");
            // context.fillText("Delivery fee",15,640);
            // context.font = ("32px Arial");
            // context.fillText("",15,680);
            // context.font = ("1px Arial");
            // context.fillText("Total Due",1,720);
            context.font = ("1px Arial");
            context.fillText("", 1, pageHeight);
            context.font = ("40px Arial");
            pageHeight += 40;
            context.fillText("---------------------------------------------------------------------", 15, pageHeight);
            context.textAlign = "center";
            context.font = ("bolder 32px Arial");
            pageHeight += 40;
            context.fillText($(document).find('#payment_type').text(), 300, pageHeight);
            context.font = ("40px Arial");
            pageHeight += 40;
            context.textAlign = "left";
            context.fillText("---------------------------------------------------------------------", 15, pageHeight);
            context.font = ("1px Arial");
            pageHeight += 1;
            context.fillText("", 1, pageHeight);
            context.textAlign = "center";
            context.font = ("32px Arial");
            pageHeight += 40;
            context.fillText(element.find('#customer_name').text(), 300, pageHeight);
            context.font = ("28px Arial");
            if (element.find('#delivery_address').text() != '') {
                let array = element.find('#delivery_address').text().split(',');
                for (var i = array.length - 1; i >= 0; i--) {
                    pageHeight += 40;
                    context.fillText(array[i], 300, pageHeight);
                }
            }
            context.font = ("32px Arial");
            pageHeight += 40;
            context.fillText(element.find('#contact_number').text(), 300, pageHeight);
            context.textAlign = "left";
            context.font = ("40px Arial");
            pageHeight += 40;
            context.fillText("---------------------------------------------------------------------", 15, pageHeight);
            context.font = ("1px Arial");
            pageHeight += 1;
            context.fillText("", 1, pageHeight);
            context.font = ("28px Arial");
            pageHeight += 40;
            context.textAlign = "center";
            context.fillText("ORDER ID: " + element.find('#order_id').text(), 300, pageHeight);
            pageHeight += 40;
            context.fillText("ORDER PLACED ON", 300, pageHeight);
            pageHeight += 40;
            context.fillText($(document).find('#order_time').text().replace('Place on ', ''), 300, pageHeight);
            context.textAlign = "left";
            context.font = ("1px Arial");
            pageHeight += 40;
            context.fillText("", 1, pageHeight);

            pageHeight = startBody;
            text = '';
            $('.' + className + ' .items-details .items-list').each(function (i, obj) {
                text = $(obj).find('.col.text-right').text();
                if ($(obj).hasClass('addon')) {
                    $(obj).find('.items-addons').each(function (ii, obj2) {
                        text = $(obj2).text();
                        if (text != '') {
                            console.log($(obj2).parent().find('.row.' + text.toLowerCase().split(" ").join("")));
                            $(obj2).parent().find('.row.' + text.toLowerCase().split(" ").join("")).find('.col.text-right').each(function (ii, obj2) {
                                text = $(this).text();
                                context.font = ("28px Arial");
                                context.fillText(text, 480, pageHeight);
                                pageHeight += 40;
                            });
                        }
                    });
                    if ($(obj).find('.special_instructions').text() != '') {
                        pageHeight -= 10;
                        pageHeight += 40;
                    }
                    text = '';
                    // text = $(obj).find('h6').text();
                    // if(text != ''){
                    // 	text = '+ '+$(obj).find('.col.text-right').text();
                    // 	context.font = ("24px Arial");
                    // }
                } else if (text != '') {
                    context.font = ("32px Arial");
                }
                if (text != '') {
                    context.fillText(text, 480, pageHeight);
                    pageHeight += 40;
                }
            });
            // context.font = ("32px Arial");
            // context.fillText("07.00",500,360); //"1 x Product"
            // context.font = ("32px Arial");
            // context.fillText("05.00",500,400); //"1 x Product"
            // context.font = ("32px Arial");
            // context.fillText("03.00",500,440); //"2 x Product"
            // context.font = ("32px Arial");
            // context.fillText("00.50",500,480); //"+ Ingredient 1"
            // context.font = ("32px Arial");
            // context.fillText("00.50",500,520); //"+ Ingredient 2"

            pageHeight += 80;
            $('.' + className + ' .summary .summary-items').each(function (i, obj) {
                text = $(obj).find('.summary-value').text();
                context.font = ("32px Arial");
                context.fillText(text, 480, pageHeight);
                pageHeight += 40;
            });
            if (!isShow)
                onSendMessage();
        }
    }

    function onSendMessage() {
        var url = 'https://192.168.1.10/StarWebPRNT/SendMessage';
        var papertype = '';

        var trader = new StarWebPrintTrader({
            url: url,
            papertype: papertype
        });

        trader.onReceive = function (response) {

            var msg = '- onReceive -\n\n';

            msg += 'TraderSuccess : [ ' + response.traderSuccess + ' ]\n';

            //      msg += 'TraderCode : [ ' + response.traderCode + ' ]\n';

            msg += 'TraderStatus : [ ' + response.traderStatus + ',\n';

            if (trader.isCoverOpen({
                    traderStatus: response.traderStatus
                })) {
                msg += '\tCoverOpen,\n';
            }
            if (trader.isOffLine({
                    traderStatus: response.traderStatus
                })) {
                msg += '\tOffLine,\n';
            }
            if (trader.isCompulsionSwitchClose({
                    traderStatus: response.traderStatus
                })) {
                msg += '\tCompulsionSwitchClose,\n';
            }
            if (trader.isEtbCommandExecute({
                    traderStatus: response.traderStatus
                })) {
                msg += '\tEtbCommandExecute,\n';
            }
            if (trader.isHighTemperatureStop({
                    traderStatus: response.traderStatus
                })) {
                msg += '\tHighTemperatureStop,\n';
            }
            if (trader.isNonRecoverableError({
                    traderStatus: response.traderStatus
                })) {
                msg += '\tNonRecoverableError,\n';
            }
            if (trader.isAutoCutterError({
                    traderStatus: response.traderStatus
                })) {
                msg += '\tAutoCutterError,\n';
            }
            if (trader.isBlackMarkError({
                    traderStatus: response.traderStatus
                })) {
                msg += '\tBlackMarkError,\n';
            }
            if (trader.isPaperEnd({
                    traderStatus: response.traderStatus
                })) {
                msg += '\tPaperEnd,\n';
            }
            if (trader.isPaperNearEnd({
                    traderStatus: response.traderStatus
                })) {
                msg += '\tPaperNearEnd,\n';
            }

            msg += '\tEtbCounter = ' + trader.extractionEtbCounter({
                traderStatus: response.traderStatus
            }).toString() + ' ]\n';

            //      msg += 'Status : [ ' + response.status + ' ]\n';
            //
            //      msg += 'ResponseText : [ ' + response.responseText + ' ]\n';

            alert(msg);
        }

        trader.onError = function (response) {
            var msg = '- onError -\n\n';

            msg += '\tStatus:' + response.status + '\n';

            msg += '\tResponseText:' + response.responseText + '\n\n';

            msg += 'Do you want to retry?\n';

            var answer = confirm(msg);

            if (answer) {
                onSendMessage();
            }
        }

        try {
            var canvas = document.getElementById('canvasPaper');

            if (canvas.getContext) {
                var context = canvas.getContext('2d');

                var builder = new StarWebPrintBuilder();

                var request = '';

                request += builder.createInitializationElement();

                request += builder.createBitImageElement({
                    context: context,
                    x: 0,
                    y: 0,
                    width: canvas.width,
                    height: canvas.height
                });

                request += builder.createCutPaperElement({
                    feed: true
                });

                trader.sendMessage({
                    request: request
                });
            }
        } catch (e) {
            alert(e.message);
        }
    }

    var urll = window.location.href;
    if (urll.indexOf('orders') != -1 && urll.indexOf('new') != -1) {
        setInterval(function () {
            get_ordr_count()
        }, 10000);
    }

    function get_ordr_count() {
        var origin = window.location.origin;
        $.ajax({
            url: origin + '/backoffice/apibackend/getOrdersCount',
            type: 'post',
            success: function (data) {

                var ttlordr = data.details.all_orders;
                var olditm = localStorage.getItem("oldnumbrordr");
                if (olditm == null) {
                    localStorage.setItem('oldnumbrordr', ttlordr);
                } else {
                    localStorage.setItem('oldnumbrordr', ttlordr);
                    if (ttlordr > olditm) {
                        location.reload();
                    }

                }


            }
        });
    };


})(jQuery);