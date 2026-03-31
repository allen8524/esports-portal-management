# NEXUS

Laravel 기반 e스포츠 포털 관리 웹 애플리케이션입니다.

이 프로젝트는 단순한 정보 조회 사이트가 아니라, 선수·팀·경기·순위·뉴스·패치 노트까지 하나의 흐름으로 관리할 수 있는 통합형 e스포츠 포털을 목표로 구성되어 있습니다.  
프론트 화면은 실제 e스포츠 미디어 사이트처럼 보이도록 구성되어 있고, 백엔드에서는 CRUD, 인증, 관리자 화면, 데이터 집계, 파일 업로드, 슬러그 라우팅, 검색/필터링 기능을 함께 다룹니다.

이 README는 업로드된 프로젝트 파일의 실제 코드 구조를 기준으로 정리했습니다.

## 1. 프로젝트 한 줄 소개

- 프로젝트명: NEXUS
- 성격: Laravel 개인 프로젝트
- 주제: e스포츠 포털 및 관리 시스템
- 주요 대상 데이터: 선수, 팀, 경기, 뉴스, 패치 노트, 회원 계정
- 특징: 사용자용 포털 화면과 관리자용 관리 화면이 함께 존재하는 구조

## 2. 프로젝트 목표

이 프로젝트의 핵심 목적은 다음과 같습니다.

- e스포츠 관련 정보를 한곳에서 조회할 수 있는 포털 화면 구현
- 선수, 팀, 경기, 뉴스 데이터를 직접 등록·수정·삭제할 수 있는 관리 기능 구현
- 경기 결과를 바탕으로 팀 순위를 자동 집계하는 기능 구현
- 회원가입, 로그인, 계정 관리, 관리자 전용 접근 제어까지 포함한 웹 서비스 구조 경험
- Laravel의 라우팅, 미들웨어, Eloquent ORM, 파일 업로드, Blade 템플릿, 페이지네이션, 캐시 활용 경험 축적

## 3. 주요 기능

### 3-1. 메인 홈 화면

홈 화면은 여러 섹션이 조합된 포털형 구조입니다.

포함된 섹션은 다음과 같습니다.

- 히어로 섹션
  - 가장 가까운 예정 경기 1건을 자동으로 조회해 메인 배너에 표시
  - 경기 제목이 있으면 제목 우선 표시
  - 제목이 없으면 팀1 VS 팀2 형식으로 자동 구성
  - 리그, 스테이지, Bo 정보까지 함께 표시
  - 경기 상세 페이지로 바로 이동 가능

- 트렌딩 뉴스 섹션
  - 최신 뉴스 또는 조회수 기반 뉴스 목록을 슬라이더 형식으로 노출

- 경기 섹션
  - 예정 경기 목록
  - 최근 종료 경기 목록
  - 팀 로고, 경기 날짜, 매치업, 결과 확인 가능

- 패치 노트 섹션
  - 최신 리그 오브 레전드 패치 노트 노출
  - 버전 정보와 공개일 표시
  - 버전 기반 공식 패치 노트 링크 생성 로직 포함

- 최신 뉴스 섹션
  - 대표 기사 1건 + 최신 기사 여러 건 구성
  - 기사 썸네일, 카테고리, 발행일, 조회수 확인 가능

- 팀 순위 미리보기
  - 종료된 경기 결과를 기반으로 간단 순위표 생성
  - 팀명, 경기 수, 승/패, 승점 표시
  - 전체 순위 페이지로 이동 가능

- 실시간 급상승 섹션
  - 경기 VOD 링크가 있는 데이터를 중심으로 영상 슬라이더 구성
  - YouTube 썸네일 추출 로직을 사용해 영상 카드 구성

- 인기 뉴스 / 시청자 투표 섹션
  - 조회수 상위 뉴스 노출
  - 팀 / 선수 인기 투표 형태의 시각적 섹션 포함

즉, 홈 화면 하나만 봐도 이 프로젝트가 단순 CRUD 과제가 아니라 실제 서비스형 포털 UI를 지향한다는 점이 드러납니다.

### 3-2. 선수 관리 기능

선수 기능은 조회와 관리가 분리되어 있습니다.

조회 측면에서는 다음을 지원합니다.

- 이름, 닉네임, 슬러그 기준 검색
- 포지션별 필터
- 팀별 필터
- 활동 중 선수만 보기
- 페이지네이션
- 카드형 UI로 선수 목록 표시

관리 측면에서는 다음을 지원합니다.

- 선수 등록
- 선수 수정
- 선수 삭제
- 실명, 닉네임, 슬러그, 포지션, 국가코드, 생년월일, 소속 팀, 활동 여부, 입단일/탈퇴일 입력
- 사진 업로드 또는 외부 URL 입력
- slug 자동 생성
- 활동 여부 boolean 처리

선수 상세 페이지는 slug 기반 라우팅으로 동작하도록 설계되어 있어, URL 가독성까지 고려한 구조입니다.

### 3-3. 팀 관리 기능

팀 기능 역시 조회와 관리가 나뉘어 있습니다.

조회 기능

- 팀명, 슬러그, 지역 기준 검색
- 페이지네이션
- 카드형 팀 목록 UI
- 팀 로고 표시
- 팀 상세 페이지에서 소속 선수 목록 조회

관리 기능

- 팀 등록
- 팀 수정
- 팀 삭제
- 팀명, 슬러그, 지역, 창단일, 로고, 활동 여부 입력
- 로고 업로드
- slug 자동 생성
- 로고 삭제 시 물리 파일 정리 처리

팀 상세에서는 팀 단위 정보뿐 아니라 선수 연관 관계까지 함께 보여주는 구조라서, 팀-선수 관계를 자연스럽게 확인할 수 있습니다.

### 3-4. 경기 관리 기능

경기 모듈은 이 프로젝트에서 가장 서비스성이 높은 기능 중 하나입니다.

지원 기능

- 경기 목록 조회
- 경기 등록
- 경기 수정
- 경기 삭제
- 경기 상세 조회

조회 시 필터링 조건

- 제목/리그/스테이지 검색
- 특정 팀 기준 경기 검색
- 상태별 검색
- 시작일 범위 검색

경기 데이터 항목

- 제목
- slug
- 팀1 / 팀2
- Bo1 / Bo3 / Bo5 / Bo7
- 시작 시간
- 상태(scheduled, live, finished, canceled)
- 양 팀 스코어
- 승리 팀
- 스테이지
- 리그
- VOD URL
- 비고

구현 특징

- 종료 경기에서 winner_team_id가 비어 있어도 점수 비교를 통해 자동 승자 계산
- 리그 목록 자동 추출
- 스테이지 템플릿 제공
  - Regional Split
  - Regular Season
  - Playoffs
  - MSI 관련 스테이지
  - Worlds 관련 스테이지
  - Regional Finals 등

이 덕분에 단순히 경기 1건을 저장하는 수준이 아니라, e스포츠 리그 운영 화면처럼 일정과 결과를 관리하는 느낌을 살린 구조가 되어 있습니다.

### 3-5. 순위 집계 기능

순위 페이지는 단순 정적 표가 아니라, matches 테이블의 실제 결과를 바탕으로 팀 성적을 계산합니다.

집계 기준

- status가 finished인 경기만 반영
- 홈팀(team1) / 원정팀(team2) 데이터를 단일 스키마로 정규화 후 union all
- 팀별 경기 수, 승수, 패수, 득점, 실점, 득실차, 승률 계산

지원 기능

- 리그 기준 필터
- 스테이지 기준 필터
- 경기 0팀 포함 여부 옵션
- 정렬 기준 변경
  - wins
  - winrate
  - diff
  - name
  - score_for
  - score_against
- 5분 캐시 적용

이 부분은 단순 CRUD보다 한 단계 더 나아간 데이터 가공 및 통계 집계 경험을 보여주는 기능입니다.

### 3-6. 뉴스 기능

뉴스 모듈은 포털형 서비스에서 핵심 콘텐츠 영역 역할을 합니다.

지원 기능

- 뉴스 목록 조회
- 뉴스 상세 조회
- 뉴스 등록
- 뉴스 수정
- 뉴스 삭제

조회 기능

- 제목/요약/본문 검색
- 카테고리 필터
- 최신순 / 인기순 정렬
- 공개 시점이 현재 이전인 뉴스만 노출
- 페이지네이션

관리 기능

- 카테고리 지정
- 제목, 슬러그, 요약, 본문 입력
- 커버 이미지 업로드
- 출처 URL 입력
- 상단 고정 여부 지정
- 발행 시각 설정
- 슬러그 유니크 보정
- 요약 자동 생성
- 상세 진입 시 조회수 증가

뉴스 상세 라우트는 slug 기반으로 설계되어 있어 콘텐츠 사이트다운 URL 구조를 가집니다.

### 3-7. 패치 노트 기능

패치 노트는 뉴스와 별도로 독립된 콘텐츠 영역으로 분리되어 있습니다.

지원 기능

- 패치 노트 목록 조회
- 패치 노트 상세 조회
- 게임, 버전, 제목, slug, 공개일, 대표 이미지 관리
- published 스코프 제공
- game('lol') 스코프 제공

홈 화면에서는 최신 LoL 패치 노트를 별도 섹션으로 보여주고 있어, e스포츠 포털답게 게임 메타 변화 정보까지 연결하는 흐름을 갖추고 있습니다.

### 3-8. 회원가입 / 로그인 / 계정 관리

인증은 Laravel 기본 users가 아니라 accounts 모델을 중심으로 구성되어 있습니다.

지원 기능

- 회원가입
- 로그인
- 로그아웃
- 내 계정 조회
- 계정 정보 수정
- 비밀번호 변경

구성 특징

- web guard가 accounts provider를 사용하도록 auth 설정 변경
- guest / auth 미들웨어 분리
- 세션 기반 로그인
- remember login 지원
- 계정 수정 시 이메일 중복 검사 및 비밀번호 확인 처리

### 3-9. 관리자 기능

관리자 전용 기능은 별도 prefix와 미들웨어로 보호됩니다.

관리자 영역 URL

- /admin
- /admin/db
- /admin/db/{table}

지원 기능

- 관리자 대시보드
- 전체 계정 수 / 관리자 수 표시
- 팀 수 / 선수 수 표시
- 전체 경기 수, 진행 상태별 경기 수 표시
- 뉴스 수 / 패치 노트 수 표시
- 최근 가입 계정 목록
- 다가오는 경기 목록
- 최근 종료 경기 목록
- 최근 뉴스 목록
- 리그별 경기 수 통계
- DB 테이블 목록 조회
- 특정 테이블 레코드 단순 조회

특히 /admin/db 기능은 테이블 이름과 레코드를 브라우저에서 직접 확인할 수 있게 만든 간단한 DB 브라우저로, 데이터 확인용 관리 기능을 구현했다는 점에서 프로젝트 완성도를 높여 줍니다.

### 3-10. 법적 고지 페이지

다음 정적 페이지도 포함되어 있습니다.

- Copyright notice
- Terms of use
- Privacy policy

서비스형 사이트 구조를 의식하고 구성한 흔적을 보여주는 부분입니다.

## 4. 기술 스택

### 백엔드

- PHP 8.2+
- Laravel 12
- Eloquent ORM
- Blade Template
- Laravel Middleware
- Laravel Pagination
- Laravel Cache
- Laravel Session Auth

### 프론트엔드

- Blade
- Bootstrap
- jQuery 기반 플러그인
- Owl Carousel
- Magnific Popup
- SlickNav
- Font Awesome
- 커스텀 CSS

### 데이터베이스

- MySQL 사용 전제로 설정됨
- 개발 파일 내 기본 SQLite 파일도 존재하지만, 업로드된 상태에서는 주요 테이블이 마이그레이션되어 있지 않음

### 빌드 도구

- Vite
- TailwindCSS 4 패키지 포함
- Axios
- Concurrently

### 기타 패키지

composer.json 기준으로 다음 패키지가 포함되어 있습니다.

- intervention/image-laravel
- phpoffice/phpspreadsheet
- laravel/tinker
- pestphp/pest
- pestphp/pest-plugin-laravel

다만 현재 업로드된 코드 기준으로는 이미지 리사이즈나 스프레드시트 내보내기 기능이 본격적으로 연결되어 있지는 않습니다.

## 5. 데이터 모델 요약

프로젝트의 핵심 테이블 구조는 다음과 같습니다.

### accounts

회원 계정 테이블입니다.

주요 컬럼

- id
- name
- email
- password
- remember_token
- created_at
- updated_at

코드상 Account 모델과 관리자 미들웨어는 is_admin 컬럼을 참조하고 있으므로, 실제 관리자 기능을 정상 사용하려면 accounts 테이블에 is_admin 컬럼이 필요합니다.

### teams

팀 정보 테이블입니다.

주요 컬럼

- name
- slug
- region
- founded_at
- logo_url
- is_active
- meta

### players

선수 정보 테이블입니다.

주요 컬럼

- name
- ign
- slug
- role
- country
- birthdate
- team_id
- photo_url
- is_active
- joined_at
- left_at
- meta

### matches

경기 정보 테이블입니다.

주요 컬럼

- slug
- title
- team1_id
- team2_id
- best_of
- start_at
- status
- team1_score
- team2_score
- winner_team_id
- stage
- league
- vod_url
- notes

### categories

뉴스 카테고리 테이블입니다.

주요 컬럼

- name
- slug

### news

뉴스 콘텐츠 테이블입니다.

주요 컬럼

- category_id
- title
- slug
- excerpt
- content
- cover_path
- source_url
- is_pinned
- published_at
- views

### patch_notes

패치 노트 테이블입니다.

주요 컬럼

- game
- version
- title
- slug
- published_at
- hero_image

## 6. 관계 구조

- Team 1 : N Player
- Match 는 Team 2개를 참조
- Match 는 winner_team_id로 승리 팀 참조 가능
- News 는 Category에 속할 수 있음
- Account 는 인증 주체 역할 수행

요약하면, 팀과 선수는 기본 마스터 데이터이고, 경기는 이 마스터 데이터를 참조하여 진행되며, 뉴스와 패치 노트는 서비스 콘텐츠 영역을 담당합니다.

## 7. 라우팅 구조

핵심 라우트는 다음과 같습니다.

### 공개 라우트

- /
- /players
- /players/{player:slug}
- /teams
- /teams/{team}
- /matches
- /matches/{match}
- /rankings
- /news
- /news/{news:slug}
- /patch-notes
- /patch-notes/{patchNote:slug}
- /legal/copyright-notice
- /legal/terms-of-use
- /legal/privacy-policy

### 게스트 전용

- /register
- /login

### 로그인 사용자 전용

- /logout
- /account
- /account/edit
- /account

### 관리자 전용

- /admin
- /admin/db
- /admin/db/{table}

즉, 사용자 포털 영역과 관리자 영역이 한 프로젝트 안에 공존하는 구조입니다.

## 8. 디렉터리 구조

실제 프로젝트에서 핵심적으로 봐야 할 폴더는 다음과 같습니다.

```text
app/
  Http/
    Controllers/
      Admin/
    Middleware/
    Requests/
  Models/

bootstrap/
config/
database/
  migrations/
  seeders/

public/
  css/
  img/
  js/
  *.html

resources/
  views/
    account/
    admin/
    auth/
    layouts/
    matches/
    news/
    partials/
    patch_notes/
    players/
    rankings/
    sections/
    teams/

routes/
  web.php
```

구조를 보면 역할이 비교적 명확하게 분리되어 있습니다.

- app/Models: 도메인 모델
- app/Http/Controllers: 기능별 요청 처리
- resources/views: 실제 화면 구성
- resources/views/sections: 홈 화면 섹션 단위 분리
- database/migrations: 스키마 설계
- database/seeders: 초기 데이터 입력
- public: 이미지, CSS, 템플릿 자산

## 9. 화면 구성 요약

### 홈

포털 메인, 예정 경기, 트렌딩 뉴스, 경기 결과, 패치 노트, 인기 뉴스, 순위, 영상 등

### 선수 페이지

선수 검색, 포지션 필터, 팀 필터, 활동 여부 필터, 카드형 목록

### 팀 페이지

팀 검색, 팀 카드 목록, 상세 페이지에서 로스터 확인

### 경기 페이지

경기 일정/상태 필터, 날짜 범위 필터, 상세 보기, 수정

### 순위 페이지

집계형 순위표, 리그/스테이지별 필터

### 뉴스 페이지

검색, 카테고리, 정렬, 카드형 기사 목록, 기사 상세

### 인증 페이지

회원가입, 로그인, 내 계정, 정보 수정

### 관리자 페이지

요약 대시보드, 최근 데이터, DB 테이블 브라우저

## 10. 실행 방법

아래는 일반적인 로컬 실행 절차입니다.

### 10-1. 사전 준비

필요 환경

- PHP 8.2 이상
- Composer
- MySQL
- Node.js / npm

### 10-2. 프로젝트 설치

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Windows라면 다음처럼 사용할 수도 있습니다.

```bash
copy .env.example .env
php artisan key:generate
```

### 10-3. 데이터베이스 생성

MySQL에서 사용할 데이터베이스를 먼저 생성합니다.

예시:

```sql
CREATE DATABASE nexus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

그 다음 .env 에서 DB 정보를 맞춰 줍니다.

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nexus
DB_USERNAME=root
DB_PASSWORD=비밀번호
```

### 10-4. 마이그레이션

```bash
php artisan migrate
```

### 10-5. 스토리지 링크

선수 사진, 팀 로고, 뉴스 커버 이미지를 public 에서 접근하려면 아래 명령이 필요합니다.

```bash
php artisan storage:link
```

### 10-6. 개발 서버 실행

백엔드 서버:

```bash
php artisan serve
```

프론트 빌드:

```bash
npm run dev
```

또는 composer.json의 dev 스크립트를 사용할 수 있습니다.

```bash
composer run dev
```

## 11. 초기 데이터 준비 방법

현재 업로드된 코드 기준으로는 데이터가 완전히 자동으로 채워지는 구조는 아닙니다.  
특히 인증은 accounts 테이블을 사용하므로, 직접 계정을 하나 만들어 두는 것이 좋습니다.

### 11-1. 패치 노트 시드

```bash
php artisan db:seed --class=PatchNoteSeeder
```

### 11-2. 뉴스 시드

뉴스는 Category 와 News 데이터를 함께 구성해야 하므로 아래처럼 실행할 수 있습니다.

```bash
php artisan db:seed --class=NewsSeeder
```

### 11-3. 일반 사용자 계정 생성 예시

```bash
php artisan tinker
```

```php
use App\Models\Account;
use Illuminate\Support\Facades\Hash;

Account::create([
    'name' => 'user',
    'email' => 'user@example.com',
    'password' => Hash::make('password1234'),
]);
```

### 11-4. 관리자 계정 생성 예시

관리자 기능을 사용하려면 accounts 테이블에 is_admin 컬럼이 있어야 합니다.

예시 마이그레이션:

```bash
php artisan make:migration add_is_admin_to_accounts_table --table=accounts
```

마이그레이션 파일 예시:

```php
Schema::table('accounts', function (Blueprint $table) {
    $table->boolean('is_admin')->default(false)->after('password');
});
```

실행:

```bash
php artisan migrate
```

그 다음 tinker 로 관리자 계정을 생성합니다.

```php
Account::create([
    'name' => 'admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('password1234'),
    'is_admin' => true,
]);
```

## 12. 코드 기준으로 확인한 참고 사항

이 항목은 README를 더 현실적으로 만들기 위해, 업로드된 코드를 직접 읽고 확인한 내용을 정리한 것입니다.

### 12-1. accounts 테이블과 관리자 컬럼

Account 모델, 관리자 미들웨어, 관리자 대시보드는 모두 is_admin 컬럼을 사용합니다.  
하지만 업로드된 migration 파일의 accounts 테이블 생성 코드에는 is_admin 컬럼이 없습니다.

즉, 관리자 기능을 실제로 쓰려면 accounts 테이블에 is_admin 컬럼을 추가하는 보완이 필요합니다.

### 12-2. 인증 테이블은 users가 아니라 accounts

Laravel 기본 구조에서는 users 테이블을 많이 사용하지만, 이 프로젝트는 실제 로그인 대상이 accounts 입니다.  
config/auth.php 역시 accounts provider 로 맞춰져 있습니다.

즉, users 테이블에 테스트 계정을 넣어도 로그인에는 사용되지 않습니다.

### 12-3. 기본 DatabaseSeeder는 accounts를 채우지 않음

현재 DatabaseSeeder 는 PatchNoteSeeder 와 User factory 를 호출합니다.  
하지만 실제 로그인은 Account 모델을 사용하므로, 시드만 돌린다고 바로 로그인 가능한 구조는 아닙니다.

### 12-4. public 안의 정적 html 파일은 템플릿 잔재로 보임

public 폴더에 blog.html, schedule.html, result.html 같은 정적 HTML 파일이 남아 있습니다.  
하지만 실제 서비스 라우팅은 resources/views 와 routes/web.php 를 기준으로 동작합니다.

즉, 실사용 핵심은 Blade 기반 화면이며, public/*.html 파일은 템플릿 참고 파일 또는 초기 자산으로 보는 것이 자연스럽습니다.

### 12-5. SQLite 파일은 존재하지만 주요 테이블이 없음

database/database.sqlite 파일은 들어 있지만, 업로드된 상태에서 players, teams, matches, news, patch_notes, accounts 테이블이 생성된 상태는 아니었습니다.

즉, 실제 실행은 MySQL 중심으로 준비하는 편이 맞습니다.

### 12-6. 로그인 화면의 예시 관리자 계정 문구

로그인 화면에는 admin@induk.ac.kr / asdf1234 예시가 안내 문구로 적혀 있습니다.  
다만 코드만 기준으로는 해당 계정이 자동 생성되는 시드는 보이지 않았습니다.

따라서 실제 로그인 가능 여부는 데이터베이스에 해당 계정이 존재하는지에 따라 달라집니다.

## 13. 이 프로젝트에서 드러나는 백엔드 역량 포인트

이 프로젝트는 단순 게시판이 아니라, 아래와 같은 역량을 보여주기 좋습니다.

- 모델 간 관계 설계
- slug 기반 라우팅
- 파일 업로드 처리
- 검색 / 필터 / 정렬 / 페이지네이션
- 경기 결과 기반 순위 집계
- 캐시 적용
- 사용자 / 관리자 권한 분리
- 관리자 대시보드 구현
- DB 브라우저 구현
- 서비스형 포털 화면 구성

특히 경기 결과를 기반으로 순위를 계산하는 부분은 “데이터를 저장하는 앱”을 넘어서 “저장된 데이터를 가공해 보여주는 앱”이라는 점에서 의미가 큽니다.

## 14. 포트폴리오에서 강조하기 좋은 부분

이 프로젝트를 포트폴리오용으로 소개할 때는 아래 흐름으로 정리하면 좋습니다.

- e스포츠 포털이라는 명확한 도메인 선택
- 사용자용 화면과 관리자 화면을 동시에 구현
- 선수/팀/경기/뉴스를 분리된 도메인으로 관리
- 경기 결과 데이터를 이용한 순위 자동 집계
- 이미지 업로드 및 콘텐츠 관리 기능 포함
- 라우팅, 인증, 권한, CRUD, 집계를 하나의 Laravel 프로젝트 안에서 경험

즉, “예쁜 화면 하나 만든 프로젝트”가 아니라 “서비스 운영을 고려한 웹 애플리케이션”이라는 점을 강조하기 좋습니다.

## 15. 개선 아이디어

코드 구조를 기준으로 앞으로 확장하기 좋은 방향도 분명합니다.

- accounts 테이블 마이그레이션 정리 및 관리자 시드 추가
- 뉴스 / 팀 / 선수 / 경기 더미 데이터 시드 정리
- 댓글, 좋아요, 북마크 기능 추가
- 경기 상세에 세트별 전적, 밴픽, VOD 목록 추가
- 선수 상세에 시즌 기록 및 이적 히스토리 추가
- 팀 상세에 최근 경기 및 순위 연동
- 관리자 화면에 차트 시각화 추가
- DB 브라우저를 읽기 전용에서 검색/필터 가능한 형태로 개선
- API 분리 후 Vue/React 프론트와 연동 가능 구조로 확장
- 테스트 코드 보강

## 16. 테스트 관련 상태

프로젝트에는 Pest, PHPUnit 설정이 포함되어 있습니다.

하지만 업로드된 파일 기준으로는 실질적인 기능 테스트 코드가 거의 없는 상태에 가깝습니다.  
즉, 테스트 환경은 준비되어 있지만, 본격적인 테스트 케이스 작성은 앞으로 보강할 수 있는 영역입니다.

## 17. 마무리

NEXUS는 Laravel을 사용해 e스포츠 포털이라는 구체적인 도메인을 구현한 프로젝트입니다.  
선수, 팀, 경기, 뉴스, 패치 노트, 회원 인증, 관리자 기능, 순위 집계까지 하나의 서비스 흐름 안에 담고 있다는 점이 가장 큰 강점입니다.

특히 다음 두 가지가 이 프로젝트의 핵심 가치입니다.

- 단순 CRUD를 넘어 실제 서비스형 포털 구조를 지향했다는 점
- 경기 결과 데이터를 활용해 순위와 콘텐츠를 유기적으로 연결했다는 점

포트폴리오에서는 다음과 같이 소개할 수 있습니다.

“Laravel 기반 e스포츠 포털 관리 시스템으로, 선수·팀·경기·뉴스·패치 노트를 통합 관리하고 경기 결과 기반 순위 집계 및 관리자 대시보드를 구현한 프로젝트”

## 18. 라이선스

프로젝트 내부의 원본 Laravel 기본 README는 MIT 라이선스 안내를 포함하고 있었으며, 현재 프로젝트 역시 Laravel 생태계 위에서 동작합니다.  
별도의 프로젝트 라이선스 정책을 적용하려면 이 README 하단에 라이선스 항목을 추가로 명시하면 됩니다.
