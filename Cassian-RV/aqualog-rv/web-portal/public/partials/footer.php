					<footer class="portal-footer">
						<div class="ui segment card portal-footer-card">
							<p><?= h(t('footer_text')); ?></p>
						</div>
					</footer>
				</main>
			</div>
		</div>
	</div>
	<div class="ui small modal" id="portal-config-modal">
		<div class="header"><?= h(t('nav_control')); ?></div>
		<div class="content">
			<div class="ui stackable two column grid">
				<div class="column">
					<div class="ui toggle checkbox">
						<input type="checkbox" checked>
						<label><?= h(t('theme_day')); ?></label>
					</div>
				</div>
				<div class="column">
					<div class="ui toggle checkbox">
						<input type="checkbox">
						<label><?= h(t('theme_night')); ?></label>
					</div>
				</div>
			</div>
		</div>
		<div class="actions">
			<div class="ui approve button">确定</div>
		</div>
	</div>
	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.5.0/dist/semantic.min.js"></script>
	<script>
	$(function () {
		var sidebarKey = 'yxt-sidebar-collapsed';
		if (window.localStorage) {
			var savedSidebar = window.localStorage.getItem(sidebarKey);
			if (savedSidebar === null) {
				document.body.classList.add('sidebar-collapsed');
				window.localStorage.setItem(sidebarKey, '1');
			} else if (savedSidebar === '1') {
				document.body.classList.add('sidebar-collapsed');
			}
		}
		$('.ui.dropdown').dropdown();
		$('.ui.checkbox').checkbox();
		$('.ui.modal').modal({ autofocus: false });
		$('.js-open-modal').on('click', function (event) {
			event.preventDefault();
			$('#portal-config-modal').modal('show');
		});
		$('.portal-sidebar-column').on('mouseenter', function () {
			if (document.body.classList.contains('sidebar-collapsed')) {
				document.body.classList.add('sidebar-peek');
			}
		});
		$('.portal-sidebar-column').on('mouseleave', function () {
			document.body.classList.remove('sidebar-peek');
		});
		$('.card-visibility-checkbox').on('change', function () {
			$(this).closest('form').trigger('submit');
		});

		function bindMiniChartDots() {
			$('.portal-mini-chart-frame .chart-dot, .portal-history-chart-frame .chart-dot').off('.tooltip');
			$('.portal-mini-chart-frame .chart-dot, .portal-history-chart-frame .chart-dot').on('mouseenter.tooltip', function () {
				var dot = $(this);
				var frame = dot.closest('.portal-mini-chart-frame, .portal-history-chart-frame');
				var tooltip = frame.find('.portal-chart-tooltip');
				var label = dot.data('label') || '';
				var time = dot.data('time') || '';
				var value = dot.data('value') || '';
				tooltip.text((label ? label + ' / ' : '') + time + ' / ' + value).addClass('is-visible');
			});
			$('.portal-mini-chart-frame .chart-dot, .portal-history-chart-frame .chart-dot').on('mousemove.tooltip', function (event) {
				var dot = $(this);
				var frame = dot.closest('.portal-mini-chart-frame, .portal-history-chart-frame');
				var tooltip = frame.find('.portal-chart-tooltip');
				var offset = frame.offset();
				tooltip.css({
					left: event.pageX - offset.left + 12,
					top: event.pageY - offset.top - 26
				});
			});
			$('.portal-mini-chart-frame .chart-dot, .portal-history-chart-frame .chart-dot').on('mouseleave.tooltip', function () {
				$(this).closest('.portal-mini-chart-frame, .portal-history-chart-frame').find('.portal-chart-tooltip').removeClass('is-visible');
			});
		}

		function updateDashboard() {
			if (!$('#dashboard-parameter-grid').length)
				return;

			var pondCode = window.dashboardInitialState ? window.dashboardInitialState.pondCode : '';
			$.getJSON('api/dashboard_data.php', { pond: pondCode }).done(function (response) {
				if (!response.ok)
					return;

				if (response.pond_name) {
					$('.portal-dashboard-header h3').text(response.pond_name);
				}

				if (response.sampled_at) {
					$('#dashboard-sampled-at').text('更新于 ' + response.sampled_at);
				}

				$.each(response.cards, function (_, card) {
					var root = $('[data-parameter-key="' + card.key + '"]');
					if (!root.length)
						return;

					root.find('.js-parameter-value').text(card.display_value);
					root.find('.js-parameter-unit').text(card.unit && card.display_value !== '未接入' && card.display_value !== '暂无数据' ? card.unit : '');
					root.find('.js-parameter-status').removeClass('red orange green grey').addClass({
						'critical': 'red',
						'warning': 'orange',
						'healthy': 'green',
						'normal': 'green',
						'neutral': 'grey'
					}[card.status] || 'grey').text({
						'critical': '严重',
						'warning': '预警',
						'healthy': '正常',
						'normal': '正常',
						'neutral': '未接入'
					}[card.status] || card.status);
					root.find('.js-parameter-band').removeClass('portal-threshold-band-low portal-threshold-band-normal portal-threshold-band-high portal-threshold-band-no-data')
						.addClass('portal-threshold-band-' + card.band_class);
					root.find('.js-parameter-band-text').text(card.band_text);

					var line = root.find('.js-card-chart-line');
					if (line.length) {
						line.attr('points', card.chart_points || '');
					}

					var dotsRoot = root.find('.js-card-chart-dots');
					if (dotsRoot.length) {
						dotsRoot.empty();
						$.each(card.chart_dots || [], function (_, dot) {
							var circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
							circle.setAttribute('class', 'chart-dot');
							circle.setAttribute('cx', dot.x);
							circle.setAttribute('cy', dot.y);
							circle.setAttribute('r', '4');
							circle.setAttribute('data-time', dot.time);
							circle.setAttribute('data-value', dot.value);
							dotsRoot.append(circle);
						});
					}
				});
				bindMiniChartDots();
			});
		}

		function refreshParameterOrderInput() {
			var keys = [];
			$('#parameter-sortable-list .portal-sortable-item').each(function () {
				keys.push($(this).data('parameter-key'));
			});
			$('#parameter-order-input').val(keys.join(','));
		}

		var dragSource = null;
		$('#parameter-sortable-list .portal-sortable-item').on('dragstart', function (event) {
			dragSource = this;
			$(this).addClass('is-dragging');
			event.originalEvent.dataTransfer.effectAllowed = 'move';
		});
		$('#parameter-sortable-list .portal-sortable-item').on('dragend', function () {
			$(this).removeClass('is-dragging');
			refreshParameterOrderInput();
		});
		$('#parameter-sortable-list .portal-sortable-item').on('dragover', function (event) {
			event.preventDefault();
			event.originalEvent.dataTransfer.dropEffect = 'move';
		});
		$('#parameter-sortable-list .portal-sortable-item').on('drop', function (event) {
			event.preventDefault();
			if (!dragSource || dragSource === this)
				return;
			if ($(dragSource).index() < $(this).index())
				$(this).after(dragSource);
			else
				$(this).before(dragSource);
			refreshParameterOrderInput();
		});
		refreshParameterOrderInput();

		$('.portal-pond-menu .item').on('click', function (event) {
			if (!$('#dashboard-parameter-grid').length)
				return;

			event.preventDefault();
			var item = $(this);
			var href = item.attr('href') || '';
			var match = href.match(/pond=([^&]+)/);
			if (!match)
				return;

			var pondCode = decodeURIComponent(match[1]);
			if (window.dashboardInitialState) {
				window.dashboardInitialState.pondCode = pondCode;
			}

			$('.portal-pond-menu .item').removeClass('active');
			item.addClass('active');

			if (window.history && window.history.replaceState) {
				window.history.replaceState({}, '', href);
			}

			updateDashboard();
		});

		bindMiniChartDots();

		if ($('#dashboard-parameter-grid').length) {
			window.setInterval(function () {
				updateDashboard();
			}, 8000);
		}
	});
	</script>
</body>
</html>
