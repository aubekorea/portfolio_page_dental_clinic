# 오브치과 간단 관리자 설치

Cafe24 웹호스팅 PHP + MySQL 기준입니다.

## 1. DB 정보 입력

`admin/config.php`에서 아래 값을 Cafe24 MySQL 정보로 변경합니다.

```php
const AUBE_DB_HOST = 'localhost';
const AUBE_DB_NAME = '카페24_DB명';
const AUBE_DB_USER = '카페24_DB아이디';
const AUBE_DB_PASS = '카페24_DB비밀번호';
const AUBE_SETUP_KEY = '임의의긴문자열';
```

## 2. 파일 업로드

홈페이지 전체 파일을 Cafe24 FTP의 웹 루트에 업로드합니다.

예시 구조:

```text
/index.html
/comm.html
/online.html
/review.html
/style.css
/script.js
/admin/
/api/
/assets/
```

## 3. 최초 설치

브라우저에서 아래 주소로 접속합니다.

```text
https://도메인/admin/setup.php?key=AUBE_SETUP_KEY에_입력한_값
```

설치가 끝나면 기본 관리자 계정이 생성됩니다.

```text
아이디: admin
비밀번호: admin1234
```

## 4. 보안 처리

설치가 끝나면 반드시 FTP에서 `admin/setup.php`를 삭제합니다.

운영 전에는 관리자 비밀번호를 변경하는 기능을 추가하거나 DB에서 비밀번호 해시를 교체해야 합니다.

## 5. 관리자 접속

```text
https://도메인/admin/login.php
```

관리 가능 항목:

- 공지사항 등록, 수정, 삭제
- 온라인상담 확인, 답변상태 변경, 답변 저장, 삭제
- 치료후기 전후사진 경로 등록, 노출 여부 관리

