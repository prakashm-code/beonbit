    <div class="body-wrapper-inner">
        <div class="container-fluid dashboard-container">
            <div class="row">
                <div class="col-lg-8 d-flex align-items-stretch">
                    <div class="card w-100 bg-primary-subtle overflow-hidden shadow-none">
                        <div class="card-body position-relative">
                            <div class="row">
                                <div class="col-sm-7">
                                    <div class="d-flex align-items-center mb-7">
                                        <div class="rounded-circle overflow-hidden me-6">
                                            <img src="{{ asset('assets/admin/images/profile/user-1.jpg') }}"
                                                alt="modernize-img" width="40" height="40">
                                        </div>
                                        <h5 class="fw-semibold mb-0 fs-5">Welcome back Admin!</h5>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="border-end pe-4 border-muted border-opacity-10">
                                            <h3 class="mb-1 fw-semibold fs-8 d-flex align-content-center">$2,340<i
                                                    class="ti ti-arrow-up-right fs-5 lh-base text-success"></i>
                                            </h3>
                                            <p class="mb-0 text-dark">Today’s Sales</p>
                                        </div>
                                        <div class="ps-4">
                                            <h3 class="mb-1 fw-semibold fs-8 d-flex align-content-center">35%<i
                                                    class="ti ti-arrow-up-right fs-5 lh-base text-success"></i>
                                            </h3>
                                            <p class="mb-0 text-dark">Overall Performance</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <div class="welcome-bg-img mb-n7 text-end">
                                        <img src="{{ asset('assets/admin/images/backgrounds/welcome-bg.svg') }}"
                                            alt="modernize-img" class="img-fluid dashboard_img">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="row alig n-items-start">
                                <div class="col-8">
                                    <h4 class="card-title mb-9 fw-semibold"> Total Transactions </h4>
                                    <div class="d-flex align-items-center mb-3">
                                        <h4 class="fw-semibold mb-0 me-8">66</h4>
                                        <div class="d-flex align-items-center">
                                            <span
                                                class="me-2 rounded-circle bg-success-subtle text-success round-20 d-flex align-items-center justify-content-center">
                                                <i class="ti ti-arrow-up-left"></i>
                                            </span>
                                            <p class="text-dark me-1 fs-3 mb-0"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    {{-- <div class="d-flex justify-content-end">
                                        <div class="p-2 bg-primary-subtle rounded-2 d-inline-block">
                                            <img src="../assets/images/svgs/icon-master-card-2.svg" alt="modernize-img"
                                                class="img-fluid" width="24" height="24">
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                            <div id="monthly-earning" style="min-height: 56px;">
                                <div id="apexchartsmonthly-earning"
                                    class="apexcharts-canvas apexchartsmonthly-earning apexcharts-theme-light"
                                    style="width: 308px; height: 56px;"><svg id="SvgjsSvg1114" width="308"
                                        height="56" xmlns="http://www.w3.org/2000/svg" version="1.1"
                                        xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.dev"
                                        class="apexcharts-svg" xmlns:data="ApexChartsNS" transform="translate(0, 0)"
                                        style="background: transparent;">
                                        <foreignObject x="0" y="0" width="308" height="56">
                                            <div class="apexcharts-legend" xmlns="http://www.w3.org/1999/xhtml"
                                                style="max-height: 28px;"></div>
                                        </foreignObject>
                                        <rect id="SvgjsRect1118" width="0" height="0" x="0" y="0"
                                            rx="0" ry="0" opacity="1" stroke-width="0" stroke="none"
                                            stroke-dasharray="0" fill="#fefefe"></rect>
                                        <g id="SvgjsG1157" class="apexcharts-yaxis" rel="0"
                                            transform="translate(-18, 0)"></g>
                                        <g id="SvgjsG1116" class="apexcharts-inner apexcharts-graphical"
                                            transform="translate(0, 0)">
                                            <defs id="SvgjsDefs1115">
                                                <clipPath id="gridRectMasktz257ud8">
                                                    <rect id="SvgjsRect1120" width="314" height="58" x="-3" y="-1"
                                                        rx="0" ry="0" opacity="1" stroke-width="0"
                                                        stroke="none" stroke-dasharray="0" fill="#fff"></rect>
                                                </clipPath>
                                                <clipPath id="forecastMasktz257ud8"></clipPath>
                                                <clipPath id="nonForecastMasktz257ud8"></clipPath>
                                                <clipPath id="gridRectMarkerMasktz257ud8">
                                                    <rect id="SvgjsRect1121" width="312" height="60" x="-2" y="-2"
                                                        rx="0" ry="0" opacity="1" stroke-width="0"
                                                        stroke="none" stroke-dasharray="0" fill="#fff"></rect>
                                                </clipPath>
                                                <linearGradient id="SvgjsLinearGradient1126" x1="0"
                                                    y1="0" x2="0" y2="1">
                                                    <stop id="SvgjsStop1127" stop-opacity="0.1"
                                                        stop-color="var(--bs-primary)" offset="0.2"></stop>
                                                    <stop id="SvgjsStop1128" stop-opacity="0" stop-color=""
                                                        offset="1.8"></stop>
                                                    <stop id="SvgjsStop1129" stop-opacity="0" stop-color=""
                                                        offset="1"></stop>
                                                </linearGradient>
                                            </defs>
                                            <line id="SvgjsLine1119" x1="0" y1="0" x2="0"
                                                y2="56" stroke="#b6b6b6" stroke-dasharray="3"
                                                stroke-linecap="butt" class="apexcharts-xcrosshairs" x="0" y="0"
                                                width="1" height="56" fill="#b1b9c4" filter="none"
                                                fill-opacity="0.9" stroke-width="1"></line>
                                            <g id="SvgjsG1132" class="apexcharts-grid">
                                                <g id="SvgjsG1133" class="apexcharts-gridlines-horizontal"
                                                    style="display: none;">
                                                    <line id="SvgjsLine1136" x1="0" y1="0"
                                                        x2="308" y2="0" stroke="#e0e0e0"
                                                        stroke-dasharray="0" stroke-linecap="butt"
                                                        class="apexcharts-gridline"></line>
                                                    <line id="SvgjsLine1137" x1="0" y1="8"
                                                        x2="308" y2="8" stroke="#e0e0e0"
                                                        stroke-dasharray="0" stroke-linecap="butt"
                                                        class="apexcharts-gridline"></line>
                                                    <line id="SvgjsLine1138" x1="0" y1="16"
                                                        x2="308" y2="16" stroke="#e0e0e0"
                                                        stroke-dasharray="0" stroke-linecap="butt"
                                                        class="apexcharts-gridline"></line>
                                                    <line id="SvgjsLine1139" x1="0" y1="24"
                                                        x2="308" y2="24" stroke="#e0e0e0"
                                                        stroke-dasharray="0" stroke-linecap="butt"
                                                        class="apexcharts-gridline"></line>
                                                    <line id="SvgjsLine1140" x1="0" y1="32"
                                                        x2="308" y2="32" stroke="#e0e0e0"
                                                        stroke-dasharray="0" stroke-linecap="butt"
                                                        class="apexcharts-gridline"></line>
                                                    <line id="SvgjsLine1141" x1="0" y1="40"
                                                        x2="308" y2="40" stroke="#e0e0e0"
                                                        stroke-dasharray="0" stroke-linecap="butt"
                                                        class="apexcharts-gridline"></line>
                                                    <line id="SvgjsLine1142" x1="0" y1="48"
                                                        x2="308" y2="48" stroke="#e0e0e0"
                                                        stroke-dasharray="0" stroke-linecap="butt"
                                                        class="apexcharts-gridline"></line>
                                                    <line id="SvgjsLine1143" x1="0" y1="56"
                                                        x2="308" y2="56" stroke="#e0e0e0"
                                                        stroke-dasharray="0" stroke-linecap="butt"
                                                        class="apexcharts-gridline"></line>
                                                </g>
                                                <g id="SvgjsG1134" class="apexcharts-gridlines-vertical"
                                                    style="display: none;"></g>
                                                <line id="SvgjsLine1145" x1="0" y1="56"
                                                    x2="308" y2="56" stroke="transparent"
                                                    stroke-dasharray="0" stroke-linecap="butt"></line>
                                                <line id="SvgjsLine1144" x1="0" y1="1"
                                                    x2="0" y2="56" stroke="transparent"
                                                    stroke-dasharray="0" stroke-linecap="butt"></line>
                                            </g>
                                            <g id="SvgjsG1135" class="apexcharts-grid-borders"
                                                style="display: none;"></g>
                                            <g id="SvgjsG1122" class="apexcharts-area-series apexcharts-plot-series">
                                                <g id="SvgjsG1123" class="apexcharts-series"
                                                    seriesName="monthlyxearnings" data:longestSeries="true"
                                                    rel="1" data:realIndex="0">
                                                    <path id="SvgjsPath1130"
                                                        d="M0 56L0 36C6.075218678756078 32.118171961106505 34.24815688035628 2.534343774559339 51.333333333333336 3.200000000000003C68.4185097863104 3.8656562254406666 86.23020188208221 36.670014926759514 102.66666666666667 40C119.10313145125113 43.329985073240486 136.9551250028935 22.937462337842717 154 24C171.0448749971065 25.062537662157286 188.5523510423306 48.75369621743934 205.33333333333334 46.4C222.1143156243361 44.046303782560656 239.6217916695602 10.662537662157284 256.6666666666667 9.600000000000001C273.7115416637732 8.537462337842719 301.66588304111093 36.248886580190366 308 40C308 40 308 56 308 56C308 56 0 56 0 56M0 36C0 36 0 36 0 36 "
                                                        fill="url(#SvgjsLinearGradient1126)" fill-opacity="1"
                                                        stroke-opacity="1" stroke-linecap="butt" stroke-width="0"
                                                        stroke-dasharray="0" class="apexcharts-area" index="0"
                                                        clip-path="url(#gridRectMasktz257ud8)"
                                                        pathTo="M 0 56 L 0 36C6.075218678756078, 32.118171961106505, 34.24815688035628, 2.534343774559338, 51.333333333333336, 3.200000000000003S86.23020188208221, 36.670014926759514, 102.66666666666667, 40S136.9551250028935, 22.937462337842714, 154, 24S188.5523510423306, 48.75369621743934, 205.33333333333334, 46.4S239.6217916695602, 10.662537662157286, 256.6666666666667, 9.600000000000001S301.66588304111093, 36.248886580190366, 308, 40 L 308 56 L 0 56M 0 36z"
                                                        pathFrom="M -1 56 L -1 56 L 51.333333333333336 56 L 102.66666666666667 56 L 154 56 L 205.33333333333334 56 L 256.6666666666667 56 L 308 56">
                                                    </path>
                                                    <path id="SvgjsPath1131"
                                                        d="M0 36C6.075218678756078 32.118171961106505 34.24815688035628 2.534343774559339 51.333333333333336 3.200000000000003C68.4185097863104 3.8656562254406666 86.23020188208221 36.670014926759514 102.66666666666667 40C119.10313145125113 43.329985073240486 136.9551250028935 22.937462337842717 154 24C171.0448749971065 25.062537662157286 188.5523510423306 48.75369621743934 205.33333333333334 46.4C222.1143156243361 44.046303782560656 239.6217916695602 10.662537662157284 256.6666666666667 9.600000000000001C273.7115416637732 8.537462337842719 301.66588304111093 36.248886580190366 308 40C308 40 308 40 308 40 "
                                                        fill="none" fill-opacity="1" stroke="var(--bs-primary)"
                                                        stroke-opacity="1" stroke-linecap="butt" stroke-width="2"
                                                        stroke-dasharray="0" class="apexcharts-area" index="0"
                                                        clip-path="url(#gridRectMasktz257ud8)"
                                                        pathTo="M 0 36C6.075218678756078, 32.118171961106505, 34.24815688035628, 2.534343774559338, 51.333333333333336, 3.200000000000003S86.23020188208221, 36.670014926759514, 102.66666666666667, 40S136.9551250028935, 22.937462337842714, 154, 24S188.5523510423306, 48.75369621743934, 205.33333333333334, 46.4S239.6217916695602, 10.662537662157286, 256.6666666666667, 9.600000000000001S301.66588304111093, 36.248886580190366, 308, 40"
                                                        pathFrom="M -1 56 L -1 56 L 51.333333333333336 56 L 102.66666666666667 56 L 154 56 L 205.33333333333334 56 L 256.6666666666667 56 L 308 56"
                                                        fill-rule="evenodd"></path>
                                                    <g id="SvgjsG1124"
                                                        class="apexcharts-series-markers-wrap apexcharts-hidden-element-shown"
                                                        data:realIndex="0">
                                                        <g class="apexcharts-series-markers">
                                                            <circle id="SvgjsCircle1161" r="0" cx="0"
                                                                cy="0"
                                                                class="apexcharts-marker wkpy9haugi no-pointer-events"
                                                                stroke="#ffffff" fill="var(--bs-primary)"
                                                                fill-opacity="1" stroke-width="2"
                                                                stroke-opacity="0.9" default-marker-size="0"></circle>
                                                        </g>
                                                    </g>
                                                </g>
                                                <g id="SvgjsG1125" class="apexcharts-datalabels" data:realIndex="0">
                                                </g>
                                            </g>
                                            <line id="SvgjsLine1146" x1="0" y1="0" x2="308"
                                                y2="0" stroke="#b6b6b6" stroke-dasharray="0"
                                                stroke-width="1" stroke-linecap="butt"
                                                class="apexcharts-ycrosshairs"></line>
                                            <line id="SvgjsLine1147" x1="0" y1="0" x2="308"
                                                y2="0" stroke-dasharray="0" stroke-width="0"
                                                stroke-linecap="butt" class="apexcharts-ycrosshairs-hidden"></line>
                                            <g id="SvgjsG1148" class="apexcharts-xaxis" transform="translate(0, 0)">
                                                <g id="SvgjsG1149" class="apexcharts-xaxis-texts-g"
                                                    transform="translate(0, -4)"></g>
                                            </g>
                                            <g id="SvgjsG1158"
                                                class="apexcharts-yaxis-annotations apexcharts-hidden-element-shown">
                                            </g>
                                            <g id="SvgjsG1159"
                                                class="apexcharts-xaxis-annotations apexcharts-hidden-element-shown">
                                            </g>
                                            <g id="SvgjsG1160"
                                                class="apexcharts-point-annotations apexcharts-hidden-element-shown">
                                            </g>
                                        </g>
                                    </svg>
                                    <div class="apexcharts-tooltip apexcharts-theme-dark">
                                        <div class="apexcharts-tooltip-series-group" style="order: 1;"><span
                                                class="apexcharts-tooltip-marker"
                                                style="background-color: var(--bs-primary);"></span>
                                            <div class="apexcharts-tooltip-text"
                                                style="font-family: inherit; font-size: 12px;">
                                                <div class="apexcharts-tooltip-y-group"><span
                                                        class="apexcharts-tooltip-text-y-label"></span><span
                                                        class="apexcharts-tooltip-text-y-value"></span></div>
                                                <div class="apexcharts-tooltip-goals-group"><span
                                                        class="apexcharts-tooltip-text-goals-label"></span><span
                                                        class="apexcharts-tooltip-text-goals-value"></span></div>
                                                <div class="apexcharts-tooltip-z-group"><span
                                                        class="apexcharts-tooltip-text-z-label"></span><span
                                                        class="apexcharts-tooltip-text-z-value"></span></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="apexcharts-yaxistooltip apexcharts-yaxistooltip-0 apexcharts-yaxistooltip-left apexcharts-theme-dark">
                                        <div class="apexcharts-yaxistooltip-text"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card bg-primary-subtle shadow-none">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div
                                    class="round rounded text-bg-primary d-flex align-items-center justify-content-center">
                                    <i class="ti ti-user-circle"></i>
                                </div>
                                <h6 class="mb-0 ms-3">Users</h6>
                                <div class="ms-auto text-primary d-flex align-items-center">
                                    <h3 class="mb-0 fw-semibold fs-7">{{ $totalUser }}</h3>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-4">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card bg-danger-subtle shadow-none">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div
                                    class="round rounded text-bg-danger d-flex align-items-center justify-content-center">
                                    <i class="ti ti-chart-donut-3"></i>
                                </div>
                                <h6 class="mb-0 ms-3">Videos</h6>
                                <div class="ms-auto text-danger d-flex align-items-center">
                                    <h3 class="mb-0 fw-semibold fs-7">{{ $totalVideo }}</h3>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-4">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card bg-success-subtle shadow-none">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div
                                    class="round rounded text-bg-success d-flex align-items-center justify-content-center">
                                    <i class="ti ti-cards"></i>
                                </div>
                                <h6 class="mb-0 ms-3">Images</h6>
                                <div class="ms-auto text-info d-flex align-items-center">
                                    <h3 class="mb-0 fw-semibold fs-7">{{ $totalImage }}</h3>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-4">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card bg-warning-subtle shadow-none">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div
                                    class="round rounded text-bg-warning d-flex align-items-center justify-content-center">
                                    <i class="ti ti-layout-grid"></i>
                                </div>
                                <h6 class="mb-0 ms-3">Catgeories</h6>
                                <div class="ms-auto text-info d-flex align-items-center">
                                    <h3 class="mb-0 fw-semibold fs-7">{{ $totalCategory }}</h3>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-4">
                            </div>
                        </div>
                    </div>
                </div>


            </div>


        </div>
    </div>
