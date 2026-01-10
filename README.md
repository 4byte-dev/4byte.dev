# 4Byte.dev

4Byte.dev, geliştiriciler için tasarlanmış; makaleler, kurslar, haberler ve etkileşimli kod alanları (CodeSpace) sunan açık kaynaklı bir topluluk platformudur.

Proje, ölçeklenebilirlik ve bakım kolaylığı sağlamak için **Laravel Modules** yapısı üzerine inşa edilmiştir ve modern bir **React + Inertia.js** ön yüzü kullanır.

## Özellikler

Bu proje, her biri kendi iş mantığına sahip bağımsız modüllerden oluşur:

- **Modüler Mimari:** Article, Course, News, Entry, User, Tag, Category gibi ayrıştırılmış modüller.
- **CodeSpace:** Tarayıcı tabanlı kod editörü ve çalışma alanı (Monaco Editor entegrasyonu).
- **Öneri Sistemi:** Kullanıcı etkileşimlerine dayalı içerik önerileri için **Gorse** entegrasyonu.
- **Admin Paneli:** İçerik ve kullanıcı yönetimi için **Filament** tabanlı güçlü bir yönetim paneli.
- **Güçlü Arama:** **Meilisearch** ile desteklenen hızlı ve typo-tolerant (yazım hatası toleranslı) arama.
- **Sosyal Etkileşim:** Beğeni, yorum, takip etme ve kaydetme özellikleri.
- **Rol ve İzinler:** Spatie Permission ve Filament Shield ile detaylı yetkilendirme.

## Teknoloji Yığını

**Backend & Altyapı:**

- **Framework:** Laravel 11/12
- **Veritabanı:** PostgreSQL (Uygulama), MySQL (Gorse verisi)
- **Cache & Queue:** Redis
- **Arama Motoru:** Meilisearch
- **Öneri Motoru:** Gorse
- **Admin Paneli:** FilamentPHP
- **Konteynerizasyon:** Docker & Docker Compose

**Frontend:**

- **Framework:** React
- **Adaptör:** Inertia.js
- **Stil:** TailwindCSS & Shadcn UI (Radix)
- **Paket Yöneticisi:** Bun / NPM
- **Build Tool:** Vite

## Kurulum

Proje, geliştirme ortamını hızlıca ayağa kaldırmak için Docker Compose kullanır.

### Gereksinimler

- Docker ve Docker Compose
- Make (Opsiyonel, ancak önerilir)

### Adım Adım Kurulum

1.  **Repoyu klonlayın:**

    ```bash
    git clone [https://github.com/4byte-dev/4byte.dev.git](https://github.com/4bytedev/4byte.dev.git)
    cd 4byte.dev
    ```

2.  **Ortam dosyasını hazırlayın:**

    ```bash
    cp .env.example .env
    ```

3.  **Docker konteynerlerini başlatın:**

    ```bash
    docker compose --env-file .env --profile app --profile production up -d
    ```

    _(Geliştirme profili için `.env` dosyasındaki ayarları ve `Makefile` içindeki profilleri kontrol edebilirsiniz.)_

4.  **Uygulama kurulumunu tamamlayın:**
    Veritabanı tablolarını oluşturmak, izinleri ayarlamak ve örnek verileri yüklemek için aşağıdaki `make` komutlarını kullanabilirsiniz:

    ```bash
    make migrate
    make create-permissions
    make seed
    ```

    _Eğer `make` kurulu değilse, `docker exec` ile `php artisan migrate`, `php artisan db:seed` komutlarını manuel çalıştırabilirsiniz._

5.  **Erişim:**
    - **Web:** [http://localhost:8000](http://localhost:8000)
    - **Admin Paneli:** [http://localhost:8000/admin](http://localhost:8000/admin)

### Varsayılan Admin Hesabı

Seed işlemi sonrası aşağıdaki bilgilerle giriş yapabilirsiniz:

- **Email:** admin@example.com
- **Şifre:** password

## Geliştirme Komutları

`Makefile` içerisinde tanımlı bazı yararlı komutlar:

- `make up`: Konteynerleri başlatır.
- `make down`: Konteynerleri durdurur.
- `make logs`: Logları izler.
- `make shell:app`: Uygulama konteynerine terminal erişimi sağlar.
- `make test`: Testleri çalıştırır.

## Katkıda Bulunma

Katkılarınızı bekliyoruz! Lütfen bir "Pull Request" göndermeden önce mevcut testlerin geçtiğinden ve kod standartlarına (ESLint/Prettier/PHP-CS-Fixer) uyduğunuzdan emin olun.

1.  Projeyi Fork'layın.
2.  Yeni bir özellik dalı (branch) oluşturun (`git checkout -b feature/HarikaOzellik`).
3.  Değişikliklerinizi Commit'leyin (`git commit -m 'feat: HarikaOzellik eklendi'`).
4.  Dalınızı Push'layın (`git push origin feature/HarikaOzellik`).
5.  Bir Pull Request oluşturun.

### Commit Mesajı Standartları

Bu proje **Conventional Commits** standardını kullanır.  
Commit mesajları **okunabilir**, **anlamlı** ve **otomasyon dostu** olmalıdır.

#### Genel Format

<type>(<scope>): <kısa açıklama>

- **type**: Yapılan değişikliğin türü
- **scope** _(opsiyonel ama önerilir)_: Etkilenen modül veya alan
- **açıklama**: Kısa, net ve emir kipinde

| Type       | Açıklama                                |
| ---------- | --------------------------------------- |
| `feat`     | Yeni özellik                            |
| `fix`      | Hata düzeltmesi                         |
| `refactor` | Davranış değişmeden yapılan iyileştirme |
| `test`     | Test ekleme veya güncelleme             |
| `chore`    | Bakım, yapılandırma, küçük düzenlemeler |
| `ci`       | CI/CD ve pipeline değişiklikleri        |
| `docs`     | Dokümantasyon değişiklikleri            |
| `perf`     | Performans iyileştirmeleri              |
| `impl`     | Yeni servis / altyapı implementasyonu   |

Scope olarak **Laravel Modules** yapısına uygun isimler kullanılır:

user, role, article, course, news, entry, page, codespace, recommend, search, auth, ci ...

## 📄 Lisans

Bu proje **GNU General Public License v3.0 (GPL-3.0)** altında lisanslanmıştır. Daha fazla bilgi için `LICENSE` dosyasına bakabilirsiniz.
