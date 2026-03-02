/**
 * MARSPEDIA PRO - REALISTIC DATA GENERATOR (INDONESIA CONTEXT)
 * Based on Marspedia.id Ecosystem (Services, Digital Products, Games)
 */

const DATA_LIMIT = 100;

// --- HELPER FUNCTIONS ---
const rInt = (min, max) => Math.floor(Math.random() * (max - min + 1)) + min;
const rArr = (arr) => arr[Math.floor(Math.random() * arr.length)];
const rID = (prefix) => `${prefix}-${rInt(10000, 99999)}`;
const rDate = (daysBack = 30) => {
    const date = new Date();
    date.setDate(date.getDate() - rInt(0, daysBack));
    date.setHours(rInt(7, 23), rInt(0, 59), rInt(0, 59)); // Jam aktif orang Indo (07:00 - 23:00)
    return date.toISOString();
};

// --- 1. DATA MASTER: KATEGORI (Sesuai Layanan Marspedia) ---
const categories = [
    { id: 1, name: "Source Code", slug: "source-code", icon: "ri-code-s-slash-line", color: "blue" },
    { id: 2, name: "Social Media Marketing", slug: "smm", icon: "ri-hashtag", color: "purple" },
    { id: 3, name: "Jasa SEO", slug: "seo", icon: "ri-search-eye-line", color: "green" },
    { id: 4, name: "Top Up Games", slug: "games", icon: "ri-gamepad-fill", color: "orange" },
    { id: 5, name: "E-Course", slug: "course", icon: "ri-book-open-fill", color: "red" },
    { id: 6, name: "Jasa Website", slug: "web-dev", icon: "ri-layout-masonry-fill", color: "indigo" }
];

// --- 2. DATA MASTER: PRODUK GENERATOR (Meniru Produk Real Marspedia) ---
// Kita membuat generator nama produk agar variatif tapi tetap relevan
const productTemplates = [
    { cat: 1, prefix: "Source Code", names: ["Website Berita Laravel 10", "Aplikasi POS Kasir", "Company Profile v2", "Sistem Sekolah Online", "E-Commerce Multi Vendor"], suffix: ["Full Fitur", "Update 2026", "Clean Code"], priceMin: 150000, priceMax: 2500000 },
    { cat: 2, prefix: "Suntik", names: ["Followers Instagram Aktif", "Likes TikTok Permanen", "Subscriber YouTube", "Views Reel Facebook", "Review Google Maps"], suffix: ["Garansi 30 Hari", "Real Human", "Instant Drop"], priceMin: 50000, priceMax: 500000 },
    { cat: 3, prefix: "Paket SEO", names: ["Backlink Media Nasional", "Content Placement Detik", "Optimasi On-Page", "Audit Website Lengkap"], suffix: ["High DA/PA", "DoFollow", "Premium"], priceMin: 500000, priceMax: 5000000 },
    { cat: 4, prefix: "Top Up", names: ["Mobile Legends", "Free Fire", "PUBG Mobile", "Genshin Impact", "Valorant"], suffix: ["86 Diamonds", "Weekly Pass", "Starlight Member", "300 UC", "Twilight Pass"], priceMin: 10000, priceMax: 300000 },
    { cat: 6, prefix: "Jasa Pembuatan", names: ["Website Landing Page", "Web Toko Online", "Web Company Profile", "Aplikasi Android WebView"], suffix: ["Terima Beres", "Free Domain .com", "Include Hosting"], priceMin: 1000000, priceMax: 7500000 }
];

const products = Array.from({ length: DATA_LIMIT }, (_, i) => {
    const template = rArr(productTemplates);
    const coreName = rArr(template.names);
    const suffix = rArr(template.suffix);
    const fullName = `${template.prefix} ${coreName} - ${suffix}`;
    const category = categories.find(c => c.id === template.cat);
    
    // Generate Image URL dengan Text Placeholder agar rapi (karena kita tidak bisa hotlink gambar asli tanpa izin)
    const imgText = coreName.split(" ").slice(0,2).join("+");
    
    return {
        id: `PRD-${1000 + i}`,
        name: fullName,
        category_id: category.id,
        category_name: category.name,
        price: rInt(template.priceMin / 1000, template.priceMax / 1000) * 1000, // Round to thousands
        stock: template.cat === 4 || template.cat === 2 ? 99999 : rInt(0, 50), // SMM & Games Unlimited
        sold: rInt(0, 500),
        status: rInt(0, 10) > 1 ? "Active" : "Non-Active",
        image: `https://placehold.co/150x150/f1f5f9/0f172a?text=${imgText}&font=roboto`
    };
});

// --- 3. DATA MEMBER (Nama Orang Indonesia Asli) ---
const firstNames = ["Agus", "Budi", "Citra", "Dewi", "Eko", "Fajar", "Gilang", "Hana", "Indah", "Joko", "Kevin", "Lestari", "Muhammad", "Nur", "Putri", "Rizky", "Siti", "Tono", "Wahyu", "Yulia"];
const lastNames = ["Santoso", "Pratama", "Wijaya", "Saputra", "Hidayat", "Nugroho", "Kurniawan", "Sari", "Utami", "Firmansyah", "Ramadhan", "Kusuma", "Wibowo"];

const members = Array.from({ length: DATA_LIMIT }, (_, i) => {
    const fName = rArr(firstNames);
    const lName = rArr(lastNames);
    const domains = ["gmail.com", "yahoo.co.id", "outlook.com", "icloud.com"];
    
    return {
        id: `MBR-${2024000 + i}`,
        name: `${fName} ${lName}`,
        email: `${fName.toLowerCase()}.${lName.toLowerCase()}${rInt(1,99)}@${rArr(domains)}`,
        phone: `08${rArr([12, 13, 21, 22, 57, 96, 51])}-${rInt(1000, 9999)}-${rInt(100, 999)}`,
        status: rArr(["Active", "Active", "Active", "Active", "Banned"]), // User mostly active
        join_date: rDate(365), // Join setahun terakhir
        avatar: `https://ui-avatars.com/api/?name=${fName}+${lName}&background=0EA5E9&color=fff&bold=true`
    };
});

// --- 4. DATA TRANSAKSI (Relasional & Real Time Logic) ---
const transactions = Array.from({ length: DATA_LIMIT }, (_, i) => {
    const user = rArr(members);
    const product = rArr(products);
    const statusList = ["Paid", "Paid", "Paid", "Proses", "Done", "Cancel", "Unpaid"]; // Weighted probability
    const currentStatus = rArr(statusList);
    
    // Metode pembayaran populer di Indo
    const methods = [
        { name: "QRIS", icon: "ri-qr-code-line" },
        { name: "BCA Virtual Account", icon: "ri-bank-card-line" },
        { name: "Mandiri VA", icon: "ri-bank-card-line" },
        { name: "GoPay", icon: "ri-wallet-3-line" },
        { name: "ShopeePay", icon: "ri-shopping-bag-3-line" },
        { name: "Transfer Bank (Manual)", icon: "ri-file-text-line" }
    ];
    
    return {
        id: `INV/${new Date().getFullYear()}/${rInt(10,12)}/${rInt(100000,999999)}`,
        user_id: user.id,
        user_name: user.name,
        user_email: user.email,
        product_id: product.id,
        product_name: product.name,
        product_img: product.image,
        category: product.category_name,
        total_price: product.price,
        payment_method: rArr(methods).name,
        status: currentStatus,
        date: rDate(7), // Transaksi 7 hari terakhir
        ip_address: `182.253.${rInt(0,255)}.${rInt(0,255)}`, // IP Telkom/Indihome range simulasi
        flag: "🇮🇩"
    };
});

// --- 5. LOG ACTIVITY (Keamanan & Audit) ---
const activityLogs = Array.from({ length: 100 }, (_, i) => {
    const user = rArr(members);
    const actions = [
        { event: "Login Success", page: "/auth/login" },
        { event: "View Product", page: `/product/detail` },
        { event: "Add to Cart", page: "/cart" },
        { event: "Checkout Pending", page: "/checkout" },
        { event: "Payment Success", page: "/payment/callback" },
        { event: "Update Password", page: "/user/setting" }
    ];
    const action = rArr(actions);

    return {
        id: i + 1,
        user_email: user.email,
        ip_address: `114.124.${rInt(0,255)}.${rInt(0,255)}`, // IP Cellular Indo
        flag: "🇮🇩",
        device: rArr(["iPhone 14 Pro", "Samsung S23", "Xiaomi Redmi Note 12", "Windows 11 Chrome", "MacBook Air M2"]),
        browser: rArr(["Chrome Mobile", "Safari", "Edge", "Firefox"]),
        event: action.event,
        page: action.page,
        timestamp: rDate(2) // Log 2 hari terakhir
    };
});

// --- 6. AGGREGATE SUMMARY (Untuk Dashboard Cards) ---
const summary = {
    income: transactions.filter(t => t.status === 'Paid' || t.status === 'Done').reduce((acc, curr) => acc + curr.total_price, 0),
    total_trx: transactions.length,
    success_trx: transactions.filter(t => t.status === 'Done' || t.status === 'Paid').length,
    pending_trx: transactions.filter(t => t.status === 'Proses' || t.status === 'Unpaid').length,
    total_members: members.length,
    total_products: products.length
};

// --- EXPORT DATABASE ---
const db = {
    categories,
    products,
    members,
    transactions,
    activityLogs,
    summary
};

console.log("Marspedia Real Data Generated:", db);