import React from "react";
import { Link } from "react-router-dom";
import { useTranslation } from "react-i18next";
import { Button, Card, CardBody, CardHeader, Col, Row } from "reactstrap";
import DataTable from "react-data-table-component";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faPlus } from "@fortawesome/free-solid-svg-icons";

const ResultadoIndex = ({ resultados }) => {
  const { t } = useTranslation();

  const columns = [
    {
      name: t("resultado.fields.id"),
      selector: (row) => row.id,
      sortable: true,
    },
    {
      name: t("resultado.fields.name"),
      selector: (row) => row.name,
      sortable: true,
    },
    {
      name: t("resultado.fields.description"),
      selector: (row) => row.description,
      sortable: true,
    },
    {
      name: t("actions"),
      cell: (row) => (
        <Link to={`/resultados/${row.id}`} className="btn btn-sm btn-primary">
          {t("view")}
        </Link>
      ),
    },
  ];

  return (
    <div className="container mt-4">
      <Row className="mb-3">
        <Col>


            <h1>{t("resultado.title")}</h1>
        </Col>
        <Col className="text-end">
          <Link to="/resultados/create" className="btn btn-success">
            <FontAwesomeIcon icon={faPlus} /> {t("create_new")}
          </Link>
        </Col>
      </Row>
      <Card>
        <CardHeader>{t("resultado.list")}</CardHeader>
        <CardBody>
          <DataTable
            columns={columns}
            data={resultados}
            pagination
            highlightOnHover
            responsive
          />
        </CardBody>
      </Card>
    </div>
  );
};

export default ResultadoIndex;
