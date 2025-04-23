<div class="w-4/5 mx-auto py-8">
    <h1 class="text-3xl font-semibold text-gray-800">Reports</h1>

    <div class="container mx-auto flex justify-center gap-6 mt-6">
        <div class="flex flex-col justify-center items-center">
            <h2 class="text-lg text-gray-600">Residential</h2>
            <div id="hs-doughnut-chart"></div>

            <!-- Legend Indicator -->
            <div class="flex justify-center sm:justify-end items-center gap-x-4 mt-3 sm:mt-6">
                <div class="inline-flex items-center">
                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        Sale <span id="residential-sale"></span>
                    </span>
                </div>
                <div class="inline-flex items-center">
                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        Rent <span id="residential-rent"></span>
                    </span>
                </div>
                <div class="inline-flex items-center">
                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        PF <span id="residential-property-finder"></span>
                    </span>
                </div>
                <div class="inline-flex items-center">
                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        Bayut <span id="residential-bayut"></span>
                    </span>
                </div>
                <div class="inline-flex items-center">
                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        Dubizzle <span id="residential-dubizzle"></span>
                    </span>
                </div>
                <div class="inline-flex items-center">
                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        Website <span id="residential-website"></span>
                    </span>
                </div>
            </div>
            <!-- End Legend Indicator -->
        </div>
        <div class="flex flex-col justify-center items-center">
            <h2 class="text-lg text-gray-600">Commercial</h2>
            <div id="hs-doughnut-chart-2"></div>

            <!-- Legend Indicator -->
            <div class="flex justify-center sm:justify-end items-center gap-x-4 mt-3 sm:mt-6">
                <div class="inline-flex items-center">
                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        Sale <span id="commercial-sale"></span>
                    </span>
                </div>
                <div class="inline-flex items-center">
                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        Rent <span id="commercial-rent"></span>
                    </span>
                </div>
                <div class="inline-flex items-center">
                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        PF <span id="commercial-property-finder"></span>
                    </span>
                </div>
                <div class="inline-flex items-center">
                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        Bayut <span id="commercial-bayut"></span>
                    </span>
                </div>
                <div class="inline-flex items-center">
                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        Dubizzle <span id="commercial-dubizzle"></span>
                    </span>
                </div>
                <div class="inline-flex items-center">
                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        Website <span id="commercial-website"></span>
                    </span>
                </div>
            </div>
            <!-- End Legend Indicator -->
        </div>
    </div>
</div>

<script src="https://preline.co/assets/js/hs-apexcharts-helpers.js"></script>

<script>
    window.addEventListener('load', async () => {
        // Fetch data for Apex Doughnut Chart
        const filters = [{
                label: 'residentialSale',
                filter: {
                    'ufCrm37OfferingType': 'RS',
                    'ufCrm37Status': 'PUBLISHED'
                }
            },
            {
                label: 'residentialRent',
                filter: {
                    'ufCrm37OfferingType': 'RR',
                    'ufCrm37Status': 'PUBLISHED'
                }
            },
            {
                label: 'residentialPropertyFinder',
                filter: {
                    'ufCrm37Status': 'PUBLISHED',
                    'ufCrm37PfEnable': true,
                    'ufCrm37OfferingType': ['RS', 'RR']
                }
            },
            {
                label: 'residentialBayut',
                filter: {
                    'ufCrm37Status': 'PUBLISHED',
                    'ufCrm37BayutEnable': true,
                    'ufCrm37OfferingType': ['RS', 'RR']
                }
            },
            {
                label: 'residentialDubizzle',
                filter: {
                    'ufCrm37Status': 'PUBLISHED',
                    'ufCrm37DubizzleEnable': true,
                    'ufCrm37OfferingType': ['RS', 'RR']
                }
            },
            {
                label: 'residentialWebsite',
                filter: {
                    'ufCrm37Status': 'PUBLISHED',
                    'ufCrm37WebsiteEnable': true,
                    'ufCrm37OfferingType': ['RS', 'RR']
                }
            },
            {
                label: 'commercialSale',
                filter: {
                    'ufCrm37OfferingType': 'CS',
                    'ufCrm37Status': 'PUBLISHED'
                }
            },
            {
                label: 'commercialRent',
                filter: {
                    'ufCrm37OfferingType': 'CR',
                    'ufCrm37Status': 'PUBLISHED'
                }
            },
            {
                label: 'commercialPropertyFinder',
                filter: {
                    'ufCrm37Status': 'PUBLISHED',
                    'ufCrm37PfEnable': true,
                    'ufCrm37OfferingType': ['CS', 'CR']
                }
            },
            {
                label: 'commercialBayut',
                filter: {
                    'ufCrm37Status': 'PUBLISHED',
                    'ufCrm37BayutEnable': true,
                    'ufCrm37OfferingType': ['CS', 'CR']
                }
            },
            {
                label: 'commercialDubizzle',
                filter: {
                    'ufCrm37Status': 'PUBLISHED',
                    'ufCrm37DubizzleEnable': true,
                    'ufCrm37OfferingType': ['CS', 'CR']
                }
            },
            {
                label: 'commercialWebsite',
                filter: {
                    'ufCrm37Status': 'PUBLISHED',
                    'ufCrm37WebsiteEnable': true,
                    'ufCrm37OfferingType': ['CS', 'CR']
                }
            }
        ];

        let stats = {};

        // Fetch data for each filter
        try {
            for (const {
                    label,
                    filter
                }
                of filters) {
                const response = await fetch(`${API_BASE_URL}/crm.item.list`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        entityTypeId: LISTINGS_ENTITY_TYPE_ID,
                        filter,
                        select: ['id']
                    })
                });

                if (!response.ok) {
                    throw new Error(`API request failed for ${label}: ${response.statusText}`);
                }

                const data = await response.json();
                stats[label] = data.total ?? (data.result?.items?.length || 0);
            }
        } catch (error) {
            console.error('Error fetching data:', error);
            // Display error message in charts
            document.getElementById('hs-doughnut-chart').innerHTML = '<p class="text-sm text-center text-red-600">Error loading data</p>';
            document.getElementById('hs-doughnut-chart-2').innerHTML = '<p class="text-sm text-center text-red-600">Error loading data</p>';
            return;
        }

        console.log('Stats:', stats);

        // Check for no data in Residential
        if (
            stats.residentialSale === 0 &&
            stats.residentialRent === 0 &&
            stats.residentialPropertyFinder === 0 &&
            stats.residentialBayut === 0 &&
            stats.residentialDubizzle === 0 &&
            stats.residentialWebsite === 0
        ) {
            document.getElementById('hs-doughnut-chart').innerHTML = '<p class="text-sm text-center text-gray-600">No data found</p>';
        }

        // Check for no data in Commercial
        if (
            stats.commercialSale === 0 &&
            stats.commercialRent === 0 &&
            stats.commercialPropertyFinder === 0 &&
            stats.commercialBayut === 0 &&
            stats.commercialDubizzle === 0 &&
            stats.commercialWebsite === 0
        ) {
            document.getElementById('hs-doughnut-chart-2').innerHTML = '<p class="text-sm text-center text-gray-600">No data found</p>';
        }

        // Update DOM with stats
        document.getElementById('residential-sale').textContent = stats.residentialSale || 0;
        document.getElementById('residential-rent').textContent = stats.residentialRent || 0;
        document.getElementById('residential-property-finder').textContent = stats.residentialPropertyFinder || 0;
        document.getElementById('residential-bayut').textContent = stats.residentialBayut || 0;
        document.getElementById('residential-dubizzle').textContent = stats.residentialDubizzle || 0;
        document.getElementById('residential-website').textContent = stats.residentialWebsite || 0;

        document.getElementById('commercial-sale').textContent = stats.commercialSale || 0;
        document.getElementById('commercial-rent').textContent = stats.commercialRent || 0;
        document.getElementById('commercial-property-finder').textContent = stats.commercialPropertyFinder || 0;
        document.getElementById('commercial-bayut').textContent = stats.commercialBayut || 0;
        document.getElementById('commercial-dubizzle').textContent = stats.commercialDubizzle || 0;
        document.getElementById('commercial-website').textContent = stats.commercialWebsite || 0;

        // Apex Doughnut Chart - Residential
        if (document.getElementById('hs-doughnut-chart').innerHTML === '') {
            buildChart(
                '#hs-doughnut-chart',
                (mode) => ({
                    chart: {
                        height: 230,
                        width: 230,
                        type: 'donut',
                        zoom: {
                            enabled: false
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '76%'
                            }
                        }
                    },
                    series: [
                        stats.residentialSale || 0,
                        stats.residentialRent || 0,
                        stats.residentialPropertyFinder || 0,
                        stats.residentialBayut || 0,
                        stats.residentialDubizzle || 0,
                        stats.residentialWebsite || 0
                    ],
                    labels: ['Sale', 'Rent', 'PF', 'Bayut', 'Dubizzle', 'Website'],
                    legend: {
                        show: false
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: 5
                    },
                    grid: {
                        padding: {
                            top: -12,
                            bottom: -11,
                            left: -12,
                            right: -12
                        }
                    },
                    states: {
                        hover: {
                            filter: {
                                type: 'none'
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        custom: function(props) {
                            return buildTooltipForDonut(
                                props,
                                mode === 'dark' ? ['#fff', '#fff', '#000', '#000', '#000', '#000'] : ['#fff', '#fff', '#000', '#000', '#000', '#000']
                            );
                        }
                    }
                }), {
                    colors: ['#3b82f6', '#22d3ee', '#f97316', '#10b981', '#8b5cf6', '#ec4899'],
                    stroke: {
                        colors: ['rgb(255, 255, 255)']
                    }
                }, {
                    colors: ['#2563eb', '#06b6d4', '#ea580c', '#059669', '#7c3aed', '#db2777'],
                    stroke: {
                        colors: ['rgb(38, 38, 38)']
                    }
                }
            );
        }

        // Apex Doughnut Chart - Commercial
        if (document.getElementById('hs-doughnut-chart-2').innerHTML === '') {
            buildChart(
                '#hs-doughnut-chart-2',
                (mode) => ({
                    chart: {
                        height: 230,
                        width: 230,
                        type: 'donut',
                        zoom: {
                            enabled: false
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '76%'
                            }
                        }
                    },
                    series: [
                        stats.commercialSale || 0,
                        stats.commercialRent || 0,
                        stats.commercialPropertyFinder || 0,
                        stats.commercialBayut || 0,
                        stats.commercialDubizzle || 0,
                        stats.commercialWebsite || 0
                    ],
                    labels: ['Sale', 'Rent', 'PF', 'Bayut', 'Dubizzle', 'Website'],
                    legend: {
                        show: false
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: 5
                    },
                    grid: {
                        padding: {
                            top: -12,
                            bottom: -11,
                            left: -12,
                            right: -12
                        }
                    },
                    states: {
                        hover: {
                            filter: {
                                type: 'none'
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        custom: function(props) {
                            return buildTooltipForDonut(
                                props,
                                mode === 'dark' ? ['#fff', '#fff', '#000', '#000', '#000', '#000'] : ['#fff', '#fff', '#000', '#000', '#000', '#000']
                            );
                        }
                    }
                }), {
                    colors: ['#3b82f6', '#22d3ee', '#f97316', '#10b981', '#8b5cf6', '#ec4899'],
                    stroke: {
                        colors: ['rgb(255, 255, 255)']
                    }
                }, {
                    colors: ['#2563eb', '#06b6d4', '#ea580c', '#059669', '#7c3aed', '#db2777'],
                    stroke: {
                        colors: ['rgb(38, 38, 38)']
                    }
                }
            );
        }
    });
</script>