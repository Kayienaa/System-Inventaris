<script>
    if (!window.adminNotificationComponent) {
        window.adminNotificationComponent = function() {
            return {
                open: false,
                unreadCount: 0,
                notifications: [],
                pollTimer: null,
                popoverStyle: 'top: 72px; right: 16px;',
                init() {
                    this.fetchData();
                    this.pollTimer = setInterval(() => {
                        this.fetchData();
                    }, 7000);
                },
                destroy() {
                    if (this.pollTimer) clearInterval(this.pollTimer);
                },
                updatePosition() {
                    if (!this.$refs.notifButton) return;
                    const rect = this.$refs.notifButton.getBoundingClientRect();
                    const rightOffset = Math.max(12, Math.round(window.innerWidth - rect.right));
                    const topOffset = Math.round(rect.bottom + 8);
                    this.popoverStyle = `top: ${topOffset}px; right: ${rightOffset}px;`;
                },
                async fetchData() {
                    try {
                        const res = await fetch('{{ route("admin.notifications.index") }}', {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (res.ok) {
                            const data = await res.json();
                            this.unreadCount = data.unread_count || 0;
                            this.notifications = data.notifications || [];
                        }
                    } catch (e) {
                        console.error('Failed to fetch admin notifications', e);
                    }
                },
                async toggleDropdown() {
                    this.open = !this.open;
                    if (this.open) {
                        this.updatePosition();
                        this.$nextTick(() => {
                            this.updatePosition();
                        });
                        if (this.unreadCount > 0) {
                            this.markAllRead();
                        }
                    }
                },
                async markAllRead() {
                    try {
                        await fetch('{{ route("admin.notifications.mark-all-read") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        this.unreadCount = 0;
                        this.notifications = this.notifications.map(n => ({ ...n, is_read: true }));
                    } catch (e) {
                        console.error('Failed to mark notifications read', e);
                    }
                },
                async destroyAll() {
                    try {
                        await fetch('{{ route("admin.notifications.destroy-all") }}', {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        this.notifications = [];
                        this.unreadCount = 0;
                    } catch (e) {
                        console.error('Failed to delete notifications', e);
                    }
                }
            };
        };
    }
</script>

<div 
    class="relative inline-block overflow-visible" 
    x-data="{ open: false, ...(window.adminNotificationComponent ? window.adminNotificationComponent() : {}) }" 
    @keydown.escape.window="open = false"
    @scroll.window="if (open) updatePosition()"
    @resize.window="if (open) updatePosition()"
>
    {{-- Tombol Lonceng Notifikasi --}}
    <button 
        type="button" 
        x-ref="notifButton"
        @click="toggleDropdown()"
        class="{{ $buttonClass ?? 'p-2 rounded-xl text-stone-500 hover:text-stone-900 bg-stone-100/80 hover:bg-stone-200/80 dark:bg-stone-900/90 dark:text-stone-300 dark:hover:text-amber-400 dark:border dark:border-stone-800 transition-all duration-200 interactive-btn cursor-pointer relative' }}"
        title="Pemberitahuan Peminjaman"
        :aria-expanded="open"
    >
        <svg class="w-5 h-5 shrink-0 transition-transform duration-200" :class="open ? 'scale-110 text-amber-600 dark:text-amber-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>

        {{-- Red Counter Badge di Pojok Kanan Atas --}}
        <span 
            x-show="unreadCount > 0" 
            x-cloak
            x-transition:enter="transition-all ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-50"
            x-transition:enter-end="opacity-100 scale-100"
            class="absolute -top-1 -right-1 flex h-4 min-w-[16px] px-1 items-center justify-center rounded-full bg-rose-600 text-white text-[10px] font-extrabold shadow-sm ring-2 ring-white dark:ring-[#0E1420] animate-pulse"
            x-text="unreadCount > 99 ? '99+' : unreadCount"
        ></span>
    </button>

    {{-- Dropdown Popover dengan x-teleport ke body (bebas dari batasan overflow header & stacking context) --}}
    <template x-teleport="body">
        <div 
            x-show="open" 
            x-cloak
            x-ref="notifPopover"
            @click.outside="if (!$refs.notifButton || !$refs.notifButton.contains($event.target)) open = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
            :style="popoverStyle"
            class="fixed z-[9999] w-[calc(100vw-2rem)] sm:w-80 max-w-sm rounded-2xl bg-white dark:bg-[#131B2A] border border-stone-200 dark:border-stone-800 shadow-2xl overflow-hidden text-stone-800 dark:text-stone-100"
        >
            {{-- Popover Header --}}
            <div class="p-4 sm:p-4.5 border-b border-stone-100 dark:border-stone-800 flex items-center justify-between gap-2 bg-stone-50/70 dark:bg-[#0E1420]/70">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="font-heading font-bold text-sm text-stone-900 dark:text-stone-100 truncate">
                        Pemberitahuan Peminjaman
                    </span>
                    <span 
                        x-show="unreadCount > 0" 
                        x-text="unreadCount + ' baru'"
                        class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400 border border-rose-200 dark:border-rose-800/50 shrink-0"
                    ></span>
                </div>

                <button 
                    type="button" 
                    @click.stop="destroyAll()" 
                    x-show="notifications.length > 0"
                    class="text-xs font-semibold text-stone-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors cursor-pointer flex items-center gap-1 shrink-0"
                    title="Hapus semua riwayat pemberitahuan"
                >
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Hapus Semua</span>
                </button>
            </div>

            {{-- Popover Notification Items --}}
            <div class="max-h-80 overflow-y-auto divide-y divide-stone-100 dark:divide-stone-800/80 [scrollbar-gutter:stable] [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-thumb]:bg-stone-300 dark:[&::-webkit-scrollbar-thumb]:bg-stone-700 [&::-webkit-scrollbar-thumb]:rounded-full">
                <template x-for="item in notifications" :key="item.id">
                    <a 
                        :href="item.target_url" 
                        class="block p-4 sm:p-4.5 transition-colors cursor-pointer group"
                        :class="!item.is_read ? 'bg-amber-50/50 dark:bg-amber-950/20 hover:bg-amber-100/60 dark:hover:bg-amber-950/30' : 'hover:bg-stone-50 dark:hover:bg-[#1A2436]'"
                    >
                        <div class="flex items-start gap-3">
                            {{-- Icon Penanda --}}
                            <div 
                                class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 mt-0.5 shadow-xs"
                                :class="item.type === 'borrow_requested' 
                                    ? 'bg-amber-100 text-amber-900 dark:bg-amber-950/70 dark:text-amber-400 border border-amber-300/60 dark:border-amber-700/50' 
                                    : 'bg-emerald-100 text-emerald-900 dark:bg-emerald-950/70 dark:text-neon-emerald border border-emerald-300/60 dark:border-emerald-700/50'"
                            >
                                {{-- Ikon Pinjam Baru (Laptop / Arrow Down) --}}
                                <template x-if="item.type === 'borrow_requested'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </template>
                                {{-- Ikon Pengembalian (Checklist / Arrow Up) --}}
                                <template x-if="item.type !== 'borrow_requested'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </template>
                            </div>

                            {{-- Text Body --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-xs font-bold text-stone-900 dark:text-stone-100 truncate group-hover:text-[#6F4E37] dark:group-hover:text-amber-400 transition-colors" x-text="item.title"></p>
                                    <span class="text-[10px] text-stone-400 dark:text-stone-500 shrink-0 font-medium" x-text="item.time_ago"></span>
                                </div>
                                <p class="text-xs text-stone-600 dark:text-stone-300 mt-0.5 line-clamp-2 leading-relaxed" x-text="item.message"></p>
                            </div>

                            {{-- Unread Dot --}}
                            <span x-show="!item.is_read" class="w-2 h-2 rounded-full bg-amber-500 shrink-0 mt-1.5 shadow-xs"></span>
                        </div>
                    </a>
                </template>

                {{-- Empty State --}}
                <div x-show="notifications.length === 0" class="py-10 px-4 text-center">
                    <div class="w-10 h-10 rounded-2xl bg-stone-100 dark:bg-stone-800 text-stone-400 dark:text-stone-500 mx-auto flex items-center justify-center mb-2.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                    </div>
                    <p class="text-xs font-medium text-stone-500 dark:text-stone-400">
                        Belum ada aktivitas peminjaman baru.
                    </p>
                </div>
            </div>

            {{-- Popover Footer --}}
            <div class="p-2.5 bg-stone-50 dark:bg-[#0E1420] border-t border-stone-100 dark:border-stone-800 text-center">
                <a 
                    href="{{ route('admin.borrowings.index') }}" 
                    class="text-xs font-bold text-[#6F4E37] dark:text-amber-400 hover:underline block py-1"
                >
                    Lihat Semua Monitoring Peminjaman &rarr;
                </a>
            </div>
        </div>
    </template>
</div>
