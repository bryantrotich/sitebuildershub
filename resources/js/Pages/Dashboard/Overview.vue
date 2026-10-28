<template>
    <AuthenticatedLayout>
        <Head title="Overview" />
        <CCol xs="12">
            <h4>Overview</h4>
            <p class="text-muted">Key metrics across clients, postings, subscriptions and payments.</p>
        </CCol>

        <CRow :xs="{ gutter: 4 }" class="mb-4">
            <CCol v-for="card in cards" :key="card.title" :sm="6" :xl="3">
                <CWidgetStatsF :color="card.color" :title="card.title" :value="card.value" class="border-0 shadow-sm">
                    <template #icon><CIcon :icon="card.icon" size="xl" /></template>
                    <template #footer>
                        <span class="small text-body-secondary">{{ card.footer }}</span>
                    </template>
                </CWidgetStatsF>
            </CCol>
        </CRow>

        <CCard class="mb-4 border-0 shadow-sm">
            <CCardBody>
                <h5 class="card-title mb-0">Growth</h5>
                <div class="small text-body-secondary">{{ first($props.trends.labels) }} - {{ last($props.trends.labels) }}</div>
                <CChart type="bar" :data="trendData" :options="trendOptions" style="height: 300px; margin-top: 24px" />
            </CCardBody>
            <CCardFooter>
                <CRow :xs="{ cols: 1, gutter: 4 }" :sm="{ cols: 2 }" :lg="{ cols: 4 }" class="mb-2 text-center">
                    <CCol>
                        <div class="text-body-secondary">Verified Clients</div>
                        <div class="fw-semibold">{{ number(metrics.clients.verified) }} ({{ percent(metrics.clients.verified, metrics.clients.total) }}%)</div>
                        <CProgress class="mt-2" color="success" thin :value="percent(metrics.clients.verified, metrics.clients.total)" />
                    </CCol>
                    <CCol>
                        <div class="text-body-secondary">Visible Postings</div>
                        <div class="fw-semibold">{{ number(metrics.postings.visible) }} ({{ percent(metrics.postings.visible, metrics.postings.total) }}%)</div>
                        <CProgress class="mt-2" color="info" thin :value="percent(metrics.postings.visible, metrics.postings.total)" />
                    </CCol>
                    <CCol>
                        <div class="text-body-secondary">Unique Viewers</div>
                        <div class="fw-semibold">{{ number(metrics.engagement.unique_views) }} of {{ number(metrics.engagement.views) }} views</div>
                        <CProgress class="mt-2" color="warning" thin :value="percent(metrics.engagement.unique_views, metrics.engagement.views)" />
                    </CCol>
                    <CCol>
                        <div class="text-body-secondary">Invoices Paid</div>
                        <div class="fw-semibold">{{ number(metrics.invoices.paid) }} of {{ number(metrics.invoices.paid + metrics.invoices.unpaid) }}</div>
                        <CProgress class="mt-2" color="primary" thin :value="percent(metrics.invoices.paid, metrics.invoices.paid + metrics.invoices.unpaid)" />
                    </CCol>
                </CRow>
            </CCardFooter>
        </CCard>

        <CRow>
            <CCol :lg="6">
                <CCard class="mb-4 border-0 shadow-sm">
                    <CCardHeader>Subscription Plans</CCardHeader>
                    <CCardBody>
                        <h6 v-if="isEmpty($props.plans)" class="mb-0 text-center"><CIcon icon="cil-ban" /> No plans yet.</h6>
                        <div v-for="plan in $props.plans" :key="plan.id" class="progress-group mb-3">
                            <div class="progress-group-header">
                                <span class="title">{{ plan.name }} <span class="small text-body-secondary">({{ plan.currency_price }})</span></span>
                                <span class="ms-auto fw-semibold">
                                    {{ number(plan.active_count) }}
                                    <span class="small text-body-secondary">({{ percent(plan.active_count, metrics.subscriptions.active) }}%)</span>
                                </span>
                            </div>
                            <div class="progress-group-bars">
                                <CProgress thin color="success" :value="percent(plan.active_count, metrics.subscriptions.active)" />
                            </div>
                        </div>
                    </CCardBody>
                </CCard>
            </CCol>
            <CCol :lg="6">
                <CCard class="mb-4 border-0 shadow-sm">
                    <CCardHeader>Engagement</CCardHeader>
                    <CCardBody>
                        <CRow>
                            <CCol v-for="item in engagement" :key="item.title" xs="6">
                                <div :class="`border-start border-start-4 border-start-${item.color} py-1 px-3 mb-3`">
                                    <div class="text-body-secondary small">{{ item.title }}</div>
                                    <div class="fs-5 fw-semibold">{{ item.value }}</div>
                                </div>
                            </CCol>
                        </CRow>
                    </CCardBody>
                </CCard>
            </CCol>
        </CRow>

        <CRow>
            <CCol :lg="6">
                <CCard class="mb-4 border-0 shadow-sm">
                    <CCardHeader>Top Postings</CCardHeader>
                    <CCardBody>
                        <CTable align="middle" hover responsive class="mb-0">
                            <CTableHead>
                                <CTableRow>
                                    <CTableHeaderCell>Title</CTableHeaderCell>
                                    <CTableHeaderCell class="text-end">Views</CTableHeaderCell>
                                    <CTableHeaderCell class="text-end">Comments</CTableHeaderCell>
                                </CTableRow>
                            </CTableHead>
                            <CTableBody>
                                <CTableRow v-if="isEmpty($props.top_postings)">
                                    <CTableDataCell colspan="3" class="text-center">Nothing Found Here.</CTableDataCell>
                                </CTableRow>
                                <CTableRow v-for="posting in $props.top_postings" :key="posting.id">
                                    <CTableDataCell>
                                        <Link :href="route('dashboard.postings.show', posting.id)">{{ posting.title }}</Link>
                                        <div class="small text-body-secondary">{{ posting.user?.name }}</div>
                                    </CTableDataCell>
                                    <CTableDataCell class="text-end">{{ number(posting.views_total) }}</CTableDataCell>
                                    <CTableDataCell class="text-end">{{ number(posting.comments_total) }}</CTableDataCell>
                                </CTableRow>
                            </CTableBody>
                        </CTable>
                    </CCardBody>
                </CCard>
            </CCol>
            <CCol :lg="6">
                <CCard class="mb-4 border-0 shadow-sm">
                    <CCardHeader class="d-flex">
                        Recent Transactions
                        <Link :href="route('dashboard.transactions')" class="ms-auto small">View all</Link>
                    </CCardHeader>
                    <CCardBody>
                        <CTable align="middle" hover responsive class="mb-0">
                            <CTableHead>
                                <CTableRow>
                                    <CTableHeaderCell>User</CTableHeaderCell>
                                    <CTableHeaderCell class="text-end">Amount</CTableHeaderCell>
                                    <CTableHeaderCell class="text-end">Status</CTableHeaderCell>
                                </CTableRow>
                            </CTableHead>
                            <CTableBody>
                                <CTableRow v-if="isEmpty($props.recent_transactions)">
                                    <CTableDataCell colspan="3" class="text-center">Nothing Found Here.</CTableDataCell>
                                </CTableRow>
                                <CTableRow v-for="transaction in $props.recent_transactions" :key="transaction.id">
                                    <CTableDataCell>
                                        {{ transaction.user?.name ?? '—' }}
                                        <div class="small text-body-secondary">{{ transaction.created_at }}</div>
                                    </CTableDataCell>
                                    <CTableDataCell class="text-end">{{ transaction.currency }} {{ number(transaction.amount) }}</CTableDataCell>
                                    <CTableDataCell class="text-end">
                                        <CBadge color="success" v-if="!isNull(transaction.paid_at)">Paid</CBadge>
                                        <CBadge color="warning" v-else>Pending</CBadge>
                                    </CTableDataCell>
                                </CTableRow>
                            </CTableBody>
                        </CTable>
                    </CCardBody>
                </CCard>
            </CCol>
        </CRow>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { CChart } from '@coreui/vue-chartjs';
import { getStyle } from '@coreui/utils';
import { Head, Link } from '@inertiajs/vue3';
import { first, isEmpty, isNull, last } from 'lodash';
import { computed } from 'vue';

const props = defineProps<{
    currency: string,
    metrics: any,
    plans: any[],
    recent_transactions: any[],
    status?: string,
    top_postings: any[],
    trends: { labels: string[], revenue: number[], clients: number[], postings: number[] },
}>();

const metrics = computed(() => props.metrics);

const number  = (value: number | string) => Number(value ?? 0).toLocaleString();
const money   = (value: number) => `${props.currency} ${Number(value ?? 0).toLocaleString(undefined, { maximumFractionDigits: 2 })}`;
const percent = (part: number, whole: number) => whole > 0 ? Math.round((part / whole) * 100) : 0;

const cards = computed(() => [
    { title: 'Clients',              value: number(metrics.value.clients.total),       icon: 'cil-people', color: 'primary', footer: `+${number(metrics.value.clients.this_month)} this month` },
    { title: 'Postings',             value: number(metrics.value.postings.total),      icon: 'cil-notes',  color: 'info',    footer: `+${number(metrics.value.postings.this_month)} this month` },
    { title: 'Revenue',              value: money(metrics.value.revenue.total),        icon: 'cil-money',  color: 'success', footer: `${money(metrics.value.revenue.this_month)} this month` },
    { title: 'Unpaid Invoices',      value: money(metrics.value.invoices.unpaid_amount), icon: 'cil-file', color: 'danger',  footer: `${number(metrics.value.invoices.unpaid)} unpaid, ${number(metrics.value.invoices.overdue)} overdue` },
    { title: 'Active Subscriptions', value: number(metrics.value.subscriptions.active), icon: 'cil-task',  color: 'warning', footer: `of ${number(metrics.value.subscriptions.total)} total` },
    { title: 'Active Promotions',    value: number(metrics.value.promotions.active),   icon: 'cil-star',   color: 'primary', footer: `of ${number(metrics.value.promotions.total)} total` },
    { title: 'Business Profiles',    value: number(metrics.value.clients.businesses),  icon: 'cil-building', color: 'info', footer: `${percent(metrics.value.clients.businesses, metrics.value.clients.total)}% of clients` },
    { title: 'Paid Transactions',    value: number(metrics.value.revenue.count),       icon: 'cil-dollar', color: 'success', footer: 'all time' },
]);

const engagement = computed(() => [
    { title: 'Posting Views',  value: number(metrics.value.engagement.views),        color: 'info' },
    { title: 'Unique Viewers', value: number(metrics.value.engagement.unique_views), color: 'warning' },
    { title: 'Comments',       value: number(metrics.value.engagement.comments),     color: 'success' },
    { title: 'Projects',       value: number(metrics.value.engagement.projects),     color: 'danger' },
]);

const trendData = computed(() => ({
    labels: props.trends.labels,
    datasets: [
        { type: 'bar',  label: `Revenue (${props.currency})`, data: props.trends.revenue,  backgroundColor: getStyle('--cui-success'), yAxisID: 'revenue', order: 2 },
        { type: 'line', label: 'New Clients',  data: props.trends.clients,  borderColor: getStyle('--cui-primary'), backgroundColor: 'transparent', tension: 0.3, yAxisID: 'count', order: 1 },
        { type: 'line', label: 'New Postings', data: props.trends.postings, borderColor: getStyle('--cui-info'),    backgroundColor: 'transparent', tension: 0.3, yAxisID: 'count', order: 1 },
    ],
}));

const trendOptions = {
    maintainAspectRatio: false,
    interaction: { mode: 'index', intersect: false },
    scales: {
        x:       { grid: { display: false } },
        revenue: { position: 'left',  beginAtZero: true },
        count:   { position: 'right', beginAtZero: true, grid: { drawOnChartArea: false }, ticks: { precision: 0 } },
    },
};
</script>
