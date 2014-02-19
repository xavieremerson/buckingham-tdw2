
create table bj_docs_staging
(
docid varchar2(100),
headline varchar2(200),
productname varchar2(100),
statusdatetime varchar2(100),
status varchar2(100)
)


create table bj_docs_main
(
docid varchar2(100),
headline varchar2(200),
productname varchar2(100),
statusdatetime varchar2(100),
status varchar2(100)
)


truncate table research_doc;
truncate table ticker_doc;
truncate table author_doc;
truncate table industry_doc;